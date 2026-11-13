// Auto-fills the Forms / Support Documents upload forms from the picked PDF.
//
// <form data-pdf-autofill="form|support_document"
//       data-pdf-autofill-mode="create|edit|revision"
//       data-check-url="..."          (code lookup endpoint)
//       data-doc-code="..."           (edit/revision: the document's current code)
//       data-exclude-id="...">        (edit: the draft being edited)
//
// Everything filled is a suggestion: fields stay editable and the server validates
// exactly as before.
import { extract } from './extract';

const FIELDS = ['title', 'code', 'pages', 'objective', 'scope'];
const TYPE_PREFIX = { form: 'F', support_document: 'SD' };
const PREFIX_LABEL = { F: 'Form', SD: 'Support Document', SP: 'System Procedure' };

let pdfjsPromise;
function loadPdfJs() {
    pdfjsPromise ??= Promise.all([
        import('pdfjs-dist'),
        import('pdfjs-dist/build/pdf.worker.min.mjs?url'),
    ]).then(([lib, worker]) => {
        lib.GlobalWorkerOptions.workerSrc = worker.default;
        return lib;
    }).catch(err => {
        pdfjsPromise = undefined; // let the next pick retry instead of failing forever
        throw err;
    });
    return pdfjsPromise;
}

function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
}

function setup(form) {
    const type = form.dataset.pdfAutofill;
    const mode = form.dataset.pdfAutofillMode || 'create';
    const fileInput = form.querySelector('input[type="file"][name="file"]');
    if (!fileInput) return;

    const fields = Object.fromEntries(FIELDS.map(name => [name, form.querySelector(`[name="${name}"]`)]).filter(([, f]) => f));
    if (mode === 'revision') delete fields.code; // read-only: the revision keeps its code
    // Section options carry the section's acronym (stored in sections.description),
    // which is the process part of the code: F-BPL-004 -> BPL.
    const sectionField = form.querySelector('select[name="section_id"]');

    const status = el('p', 'mt-2 text-xs text-gray-500 hidden');
    const warnings = el('div', 'mt-2 space-y-2');
    fileInput.after(status, warnings);

    let lastResult = null;
    let run = 0;

    function setStatus(text, tone = 'muted') {
        status.textContent = text;
        status.className = 'mt-2 text-xs ' + (tone === 'error' ? 'text-red-600' : tone === 'ok' ? 'text-sky-700' : 'text-gray-500');
        status.classList.toggle('hidden', !text);
    }

    function addWarning(group, parts, tone = 'warn') {
        const box = el('div', tone === 'ok'
            ? 'rounded-md border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-800'
            : 'rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800');
        box.dataset.group = group;
        for (const part of parts) {
            if (typeof part === 'string') box.append(part);
            else box.append(Object.assign(el('a', 'font-semibold underline', part.text), { href: part.href, target: '_blank' }));
        }
        warnings.append(box);
    }

    function clearWarnings(group) {
        warnings.querySelectorAll(group ? `[data-group="${group}"]` : '[data-group]').forEach(n => n.remove());
    }

    function hintFor(field) {
        let hint = field.parentElement.querySelector(':scope > [data-pdf-hint]');
        if (!hint) {
            hint = el('p', 'mt-1 text-xs');
            hint.dataset.pdfHint = '';
            field.after(hint);
        }
        hint.replaceChildren();
        return hint;
    }

    function removeHint(field) {
        field.parentElement.querySelector(':scope > [data-pdf-hint]')?.remove();
    }

    function fill(field, value, message = 'Filled from the PDF, please check.') {
        field.value = value;
        field.dataset.pdfValue = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
        const hint = hintFor(field);
        hint.className = 'mt-1 text-xs text-sky-700';
        hint.textContent = message;
    }

    // Offer the PDF's value without overwriting something the user (or the saved
    // document) already has.
    function suggest(field, value, { label = value, prefix = 'PDF says', message } = {}) {
        const hint = hintFor(field);
        hint.className = 'mt-1 text-xs text-gray-500';
        const shown = label.length > 120 ? label.slice(0, 120) + '…' : label;
        const use = el('button', 'ml-1 font-semibold text-sky-700 hover:underline', 'Use this');
        use.type = 'button';
        use.addEventListener('click', () => fill(field, value, message));
        hint.append(`${prefix}: “${shown.replace(/\n/g, ' ')}”`, use);
    }

    // Typing in a field drops its "filled from PDF" hint.
    for (const field of [...Object.values(fields), sectionField].filter(Boolean)) {
        field.addEventListener('input', e => {
            if (e.isTrusted && field.value !== field.dataset.pdfValue) removeHint(field);
        });
    }

    // Pick the section whose acronym matches the code's process part.
    function applySection(code) {
        if (!sectionField) return false;
        const process = code?.trim().toUpperCase().split('-')[1];
        if (!process) {
            removeHint(sectionField);
            return false;
        }

        const option = [...sectionField.options].find(o => (o.dataset.acronym || '').trim().toUpperCase() === process);
        if (!option) {
            const hint = hintFor(sectionField);
            hint.className = 'mt-1 text-xs text-amber-700';
            hint.textContent = `No section has the acronym ${process}. Please choose the section manually.`;
            return false;
        }

        const label = option.textContent.trim();
        const message = `Selected from the code (${process}), please check.`;
        const selected = sectionField.selectedOptions[0];
        const free = !selected || selected.disabled || !selected.value || sectionField.value === sectionField.dataset.pdfValue;
        if (sectionField.value === option.value) {
            removeHint(sectionField);
        } else if (free) {
            fill(sectionField, option.value, message);
        } else {
            suggest(sectionField, option.value, { label, prefix: `The code ${process} matches`, message });
        }
        return true;
    }

    function applyFields(result) {
        let filled = 0;
        for (const [name, field] of Object.entries(fields)) {
            const value = result[name] == null ? null : String(result[name]);
            if (!value) {
                removeHint(field);
                continue;
            }
            const current = field.value.trim();
            // Pages describe the file itself, so a new file always updates them.
            const free = name === 'pages' || current === '' || current === field.dataset.pdfValue;
            if (free) {
                if (current !== value) fill(field, value);
                else removeHint(field);
                filled++;
            } else if (current.replace(/\s+/g, ' ').toLowerCase() !== value.replace(/\s+/g, ' ').toLowerCase()) {
                suggest(field, value);
            } else {
                removeHint(field);
            }
        }
        return filled;
    }

    function checkPdfCode(result) {
        clearWarnings('pdf');
        if (!result.code) return;

        const prefix = result.code.split('-')[0];
        const expected = TYPE_PREFIX[type];
        if (expected && prefix !== expected) {
            addWarning('pdf', [`${result.code} looks like a ${PREFIX_LABEL[prefix] ?? prefix} code, but you are uploading a ${PREFIX_LABEL[expected]}. Check that this is the right file and document type.`]);
        }

        const docCode = form.dataset.docCode;
        if (docCode && mode !== 'create' && result.code.toUpperCase() !== docCode.toUpperCase()) {
            addWarning('pdf', [`This PDF's code is ${result.code}, but this document is ${docCode}. Check that you picked the right file.`]);
        }
    }

    const codeToCheck = () => (mode === 'revision' ? form.dataset.docCode : fields.code?.value || '').trim();

    async function checkCodeOnServer() {
        clearWarnings('server');
        const code = codeToCheck();
        if (!code || !form.dataset.checkUrl) return;

        const url = new URL(form.dataset.checkUrl, window.location.origin);
        url.searchParams.set('code', code);
        if (form.dataset.excludeId) url.searchParams.set('exclude', form.dataset.excludeId);

        let data;
        try {
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) return;
            data = await res.json();
        } catch {
            return; // the check is a convenience; never block the upload on it
        }
        if (code !== codeToCheck()) return; // the code was changed meanwhile

        if (mode === 'create' && data.active) {
            addWarning('server', [
                `${data.code} already exists (Active, Rev. ${data.active.revision ?? '-'}: ${data.active.title}). To change it, open it and choose Edit to start a new revision instead of uploading it again. `,
                { text: `Open ${data.code}`, href: data.active.url },
            ]);
        } else if (mode === 'create' && data.in_progress) {
            addWarning('server', [
                `${data.code} is already in the system (${data.in_progress.status}). `,
                { text: `Open ${data.code}`, href: data.in_progress.url },
            ]);
        }

        const pdfRevision = lastResult?.revision;
        if (pdfRevision != null && lastResult?.code?.toUpperCase() === code.toUpperCase()) {
            if (pdfRevision === data.expected_revision) {
                addWarning('server', [`The PDF's Rev. ${pdfRevision} matches the revision this will be approved as.`], 'ok');
            } else {
                addWarning('server', [`The PDF says Rev. ${pdfRevision}, but this would be approved as Rev. ${data.expected_revision}. Check the revision number in the PDF's header.`]);
            }
        }
    }

    fileInput.addEventListener('change', async () => {
        const file = fileInput.files?.[0];
        const thisRun = ++run;
        lastResult = null;
        clearWarnings();
        if (!file) {
            setStatus('');
            return;
        }

        setStatus('Reading the PDF…');
        let result;
        try {
            const pdfjs = await loadPdfJs();
            const doc = await pdfjs.getDocument({ data: await file.arrayBuffer(), isEvalSupported: false }).promise;
            try {
                result = await extract(doc, file.name);
            } finally {
                doc.destroy();
            }
        } catch (err) {
            console.error('PDF auto-fill failed:', err);
            if (thisRun === run) setStatus("Couldn't read this PDF. Please fill in the details manually.", 'error');
            return;
        }
        if (thisRun !== run) return; // another file was picked meanwhile

        lastResult = result;
        const filled = applyFields(result);
        // Match on the code actually in the form (it may have been typed rather than read).
        applySection(mode === 'revision' ? (result.code ?? form.dataset.docCode) : (fields.code?.value || result.code));
        if (!result.hasText) {
            setStatus('This PDF has no readable text (it may be a scan). Please fill in the details manually.', 'error');
        } else if (filled === 0 && !result.code && !result.title) {
            setStatus('No document details found in this PDF. Please fill in the details manually.');
        } else {
            setStatus('Details read from the PDF. Please check the highlighted fields before submitting.', 'ok');
        }

        checkPdfCode(result);
        await checkCodeOnServer();
    });

    fields.code?.addEventListener('change', () => {
        applySection(fields.code.value);
        checkCodeOnServer();
    });
}

export function init() {
    document.querySelectorAll('form[data-pdf-autofill]').forEach(setup);
}
