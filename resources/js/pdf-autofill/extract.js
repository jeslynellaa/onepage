// Reads document details (code, title, pages, revision, objective/scope) out of an
// uploaded PDF's text layer. Works on PDFs exported from Word/Excel; scanned PDFs have
// no text, so every field comes back null and the user fills the form in by hand.
//
// Code format follows the FCU coding standard (SD-DIM-01): SP/SD-YYY-NN for manuals,
// F-YYY-NNNa for forms.

const CODE_RE = /\b(SP|SD|F)\s*-\s*([A-Z]{2,4})\s*-\s*(\d{2,3}[a-z]?)\b/;
const CODE_LABEL_RE = /\b(Document|Doc\.?|Form|Reference|Ref\.?)\s*(No\.?|Number|Code)\b/i;
const REV_RE = /\bRev(?:ision)?\.?\s*(?:No\.?)?\s*:?\s*(\d+)\b/i;
const DATE_RE = /\b(\d{2})\s*\/\s*(\d{2})\s*\/\s*(\d{4})\b/;
// Only the top quarter of page 1 counts as the header; codes also appear in body text
// ("use F-MPL-03 Risk Register"), which must not be picked up.
const HEADER_FRACTION = 0.25;

// Join text fragments on one line: no space when they touch, one space otherwise.
// Word exports often split a code into pieces ("S", "D", "-", "DIM", "-", "01").
function joinPieces(pieces) {
    return pieces.reduce((acc, p, i) => {
        if (i === 0) return p.str;
        const prev = pieces[i - 1];
        return acc + (p.x - prev.right > prev.fs * 0.2 ? ' ' : '') + p.str;
    }, '').replace(/\s+/g, ' ').trim();
}

// Join the lines of a text cell, keeping a line break where a line ends well short
// of the column's right edge (end of a sentence or bullet) and re-flowing the rest.
function joinBlock(lines) {
    lines = lines.filter(l => l.pieces.length);
    if (!lines.length) return null;
    const right = Math.max(...lines.map(l => l.pieces.at(-1).right));
    const left = Math.min(...lines.map(l => l.pieces[0].x));
    return lines.reduce((acc, l, i) => {
        const text = joinPieces(l.pieces);
        if (i === 0) return text;
        const prev = lines[i - 1];
        const short = prev.pieces.at(-1).right - left < (right - left) * 0.85;
        return acc + (short ? '\n' : ' ') + text;
    }, '').replace(/ +([.,;:])/g, '$1').trim() || null;
}

// Group a page's text items into visual lines, top to bottom.
async function pageLines(page) {
    const { height } = page.getViewport({ scale: 1 });
    const { items } = await page.getTextContent();
    const pieces = items
        .filter(i => i.str && i.str.trim())
        .map(i => ({
            str: i.str,
            x: i.transform[4],
            right: i.transform[4] + i.width,
            top: height - i.transform[5],
            fs: Math.hypot(i.transform[2], i.transform[3]),
        }))
        .sort((a, b) => a.top - b.top || a.x - b.x);

    const lines = [];
    for (const p of pieces) {
        const line = lines.find(l => Math.abs(l.top - p.top) < Math.max(2, p.fs * 0.3));
        line ? line.pieces.push(p) : lines.push({ top: p.top, pieces: [p] });
    }
    for (const l of lines) {
        l.pieces.sort((a, b) => a.x - b.x);
        l.text = joinPieces(l.pieces);
        l.fs = Math.max(...l.pieces.map(p => p.fs));
        l.x = l.pieces[0].x;
    }
    lines.sort((a, b) => a.top - b.top);
    return { lines, height };
}

const isAllCaps = s => {
    const letters = s.replace(/[^A-Za-z]/g, '');
    return letters.length >= 4 && letters.replace(/[^A-Z]/g, '').length / letters.length >= 0.9;
};

const SMALL_WORDS = new Set(['a', 'an', 'and', 'as', 'at', 'by', 'for', 'in', 'of', 'on', 'or', 'per', 'the', 'to', 'vs', 'with']);
const ACRONYMS = new Set(['ISO', 'IT', 'ICT', 'HR', 'OSH', 'QMS', 'EMS', 'IMS', 'QMR', 'CEO', 'DIRF', 'SOP', 'KPI', 'PPE', 'BIR', 'SEC', 'DOLE', 'SOAR', 'SWOT', 'PESTLE', 'CCTV', 'NCR', 'CAR']);

// "RISK MANAGEMENT GUIDELINES" -> "Risk Management Guidelines". Acronyms (a known list,
// or any word without vowels like "QMS") and dotted names ("S.E.R.") stay upper case.
export function toTitleCase(text) {
    return text.split(/(\s+|[-/(])/).map((part, i) => {
        if (!/[A-Za-z]/.test(part)) return part;
        const upper = part.toUpperCase();
        if (ACRONYMS.has(upper.replace(/[^A-Z]/g, '')) || /\./.test(part) || /\d/.test(part) || !/[AEIOUY]/.test(upper)) return upper;
        const lower = part.toLowerCase();
        if (i > 0 && SMALL_WORDS.has(lower)) return lower;
        return lower.charAt(0).toUpperCase() + lower.slice(1);
    }).join('');
}

function findCode(header) {
    const labelled = header.find(l => CODE_LABEL_RE.test(l.text) && CODE_RE.test(l.text));
    const hit = labelled ?? header.find(l => CODE_RE.test(l.text));
    if (!hit) return null;
    const [, type, process, seq] = hit.text.match(CODE_RE);
    return `${type}-${process}-${seq}`;
}

// The title is the largest all-caps line near the top of page 1 (above OBJECTIVE when
// present). Restricting to caps skips the company name beside the logo, which can be
// bigger than the title; the "... MANUAL" banner and confidentiality line are skipped.
function findTitle(lines, height) {
    const objective = lines.find(l => l.pieces.some(p => /^OBJECTIVES?$/.test(p.str.trim())));
    const limit = objective ? objective.top : height * HEADER_FRACTION;
    const candidates = lines.filter(l =>
        l.top < limit &&
        isAllCaps(l.text) &&
        !/MANUAL$/.test(l.text) &&
        !CODE_RE.test(l.text) &&
        !/CONFIDENTIAL|COPY\b/.test(l.text));
    if (!candidates.length) return null;

    const best = candidates.reduce((b, l) => (l.fs > b.fs ? l : b));
    // A long title wraps: pull in the next caps line(s) of the same size right below it.
    const parts = [best];
    for (let i = candidates.indexOf(best) + 1; i < candidates.length; i++) {
        const prev = parts.at(-1);
        const next = candidates[i];
        if (Math.abs(next.fs - best.fs) > 0.5 || next.top - prev.top > best.fs * 1.6) break;
        parts.push(next);
    }
    return parts.map(l => l.text).join(' ');
}

function findRevision(header) {
    for (const l of header) {
        const m = l.text.match(REV_RE);
        if (m) return Number(m[1]);
    }
    return null;
}

function findEffectiveDate(header) {
    const hit = header.find(l => /Effective/i.test(l.text) && DATE_RE.test(l.text))
        ?? header.find(l => REV_RE.test(l.text) && DATE_RE.test(l.text))
        ?? header.find(l => DATE_RE.test(l.text));
    if (!hit) return null;
    const [, m, d, y] = hit.text.match(DATE_RE);
    return `${m}/${d}/${y}`;
}

// OBJECTIVE / SCOPE. Two layouts seen so far:
//  - side by side (FCU): both labels on one line, text in two columns below
//  - stacked (LDC, S.E.R.): labels in a left column, text in a right column
function findObjectiveScope(lines) {
    const label = re => {
        for (const l of lines) for (const p of l.pieces) if (re.test(p.str.trim())) return { line: l, piece: p };
        return null;
    };
    const obj = label(/^OBJECTIVES?$/);
    const scope = label(/^SCOPE$/);
    if (!obj || !scope) return { objective: null, scope: null };

    const lineGap = l => l.fs * 2.2;

    if (obj.line === scope.line) {
        // Walk down from the label row until a big vertical gap ends the table.
        const body = [];
        let prevTop = obj.line.top;
        for (const l of lines.filter(l => l.top > obj.line.top)) {
            if (l.top - prevTop > lineGap(l) + 8) break;
            body.push(l);
            prevTop = l.top;
        }
        if (!body.length) return { objective: null, scope: null };
        const colLeft = Math.min(...body.map(l => l.x));
        const boundary = scope.piece.x - (obj.piece.x - colLeft) - 10;
        const side = test => body.map(l => ({ ...l, pieces: l.pieces.filter(p => test(p.x)) }));
        return {
            objective: joinBlock(side(x => x < boundary)),
            scope: joinBlock(side(x => x >= boundary)),
        };
    }

    // Stacked: the text sits to the right of the label column.
    const valueX = obj.piece.right + 5;
    const valueLines = lines.map(l => {
        const pieces = l.pieces.filter(p => p.x >= valueX);
        return { ...l, pieces, text: joinPieces(pieces) };
    });
    const hasLeftText = i => lines[i].pieces.some(p => p.x < valueX && !/^(OBJECTIVES?|SCOPE)$/.test(p.str.trim()));

    // Expand upward from OBJECTIVE and downward from SCOPE until we leave the table.
    let start = lines.indexOf(obj.line);
    while (start > 0 && !hasLeftText(start - 1) && valueLines[start - 1].text
        && lines[start].top - lines[start - 1].top <= lineGap(lines[start])) start--;
    let end = lines.indexOf(scope.line);
    while (end < lines.length - 1 && !hasLeftText(end + 1) && valueLines[end + 1].text
        && lines[end + 1].top - lines[end].top <= lineGap(lines[end])) end++;

    // Labels are usually vertically centred in their row, so split at the largest
    // vertical gap between the two labels.
    const block = valueLines.slice(start, end + 1).filter(l => l.text);
    let split = block.findIndex(l => l.top >= scope.line.top - 2);
    let best = -1;
    for (let i = 1; i < block.length; i++) {
        if (block[i].top < obj.line.top - 2 || block[i].top > scope.line.top + 2) continue;
        const gap = block[i].top - block[i - 1].top;
        if (gap > best) {
            best = gap;
            split = i;
        }
    }
    if (split < 0) return { objective: null, scope: null };
    return {
        objective: joinBlock(block.slice(0, split)),
        scope: joinBlock(block.slice(split)),
    };
}

// "F-BPL-004 Risk Register_Rev 3 (1).pdf" -> { code: "F-BPL-004", title: "Risk Register" }
export function parseFilename(name) {
    const stem = name
        .replace(/\.pdf$/i, '')
        .replace(/\s*\(\d+\)$/, '')
        .replace(/[_\s]+Rev\.?\s*\d+$/i, '')
        .trim();
    const m = stem.match(/^((?:SP|SD|F)-[A-Z]{2,4}-\d{2,3}[a-z]?)[\s_-]+(.+)$/);
    return m ? { code: m[1], title: m[2].replace(/_/g, ' ').trim() } : { code: null, title: null };
}

/**
 * @param {import('pdfjs-dist').PDFDocumentProxy} doc
 * @param {string} filename the original name of the picked file
 */
export async function extract(doc, filename) {
    const { lines, height } = await pageLines(await doc.getPage(1));
    const header = lines.filter(l => l.top < height * HEADER_FRACTION);
    const fromName = parseFilename(filename);

    const code = findCode(header);
    const revision = findRevision(header);
    // Only read a title out of the PDF when its header looks like a controlled document;
    // otherwise any big caps text (a name on a CV, a banner) would be picked up.
    const controlled = Boolean(code) || revision !== null;

    let title = controlled ? findTitle(lines, height) : null;
    if (title && fromName.title && title.toLowerCase() === fromName.title.toLowerCase()) {
        title = fromName.title; // the filename already has the intended casing
    } else if (title && isAllCaps(title)) {
        title = toTitleCase(title);
    }

    return {
        hasText: lines.length > 0,
        pages: doc.numPages,
        code: code ?? fromName.code,
        title: title ?? fromName.title,
        revision,
        effectiveDate: findEffectiveDate(header),
        ...findObjectiveScope(lines),
    };
}
