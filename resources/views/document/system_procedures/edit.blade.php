<x-layout>
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 py-8 space-y-8 antialiased text-gray-900">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-400">
            <div>
                <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                    <a href="{{ route('document.index') }}" class="hover:text-blue-600 transition">Document Management</a>
                    <span>/</span>
                    <span class="text-gray-500">System Procedures</span>
                </nav>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit System Procedure</h1>
            </div>
        </div>

        <div class="space-y-8">
            <!-- MASTER CONTROLLER FORM -->
            <form id="documentForm" method="POST" 
                action="{{ isset($doc) && $doc->status !== 'Active' ? route('document.system_procedures.update', $doc->id) : route('document.system_procedures.store') }}" 
                enctype="multipart/form-data" class="space-y-8"
            >
                @csrf
                @if(isset($doc) && $doc->status !== 'Active')
                    @method('PUT')
                @endif
                
                <!-- PHASE 1: Basic Information Card -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md/50">
                    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">1</span>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Basic Document Details</h2>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            <div class="md:col-span-6">
                                <label for="title" class="block text-xs font-bold uppercase text-gray-500 tracking-wide mb-1.5">Document Title</label>
                                <input type="text" id="title" name="title" value="{{ old('title', $doc->title ?? '') }}" class="w-full rounded-lg border-gray-200 text-sm p-3 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-150" />
                                <input type="hidden" name="type" value="System Procedures">
                            </div>

                            <div class="md:col-span-2">
                                <label for="section_id" class="block text-xs font-bold uppercase text-gray-500 tracking-wide mb-1.5">Section No.</label>
                                <select name="section_id" id="section_id" class="w-full rounded-lg border-gray-200 text-sm p-3 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-150">
                                    <option disabled>-- Select --</option>
                                    @foreach($process_names as $process)
                                        <option value="{{ $process->id }}" @if(old('section_id', $doc->section_id ?? '') == $process->id) selected @endif>{{ $process->section_number }} {{ $process->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="code" class="block text-xs font-bold uppercase text-gray-500 tracking-wide mb-1.5">Document Code</label>
                                <input type="text" id="code" name="code" value="{{ old('code', $doc->code ?? '') }}" placeholder="e.g., SP-01" class="w-full rounded-lg border-gray-200 text-sm p-3 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-150" />
                            </div>

                            <div class="md:col-span-2">
                                <label for="revision_number" class="block text-xs font-bold uppercase text-gray-500 tracking-wide mb-1.5">Revision No.</label>
                                <input readonly type="text" id="revision_number" name="revision_number" value="{{ $doc->revision_number }}" placeholder="0" class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-400 font-semibold text-center text-sm p-3 cursor-not-allowed" />
                            </div>
                        </div>

                        <div>
                            <label for="justification" class="block text-xs font-bold uppercase text-gray-500 tracking-wide mb-1.5">Justification / Objective of Change</label>
                            <input required type="text" id="justification" name="justification" placeholder="State clear drivers for creating or modifying this procedure..." value="{{ old('justification', $doc->justification ?? '') }}" class="w-full rounded-lg border-gray-200 text-sm p-3 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-150" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
                                    <label for="objective" class="block text-xs font-bold uppercase text-gray-500 tracking-wide">Objective</label>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100/70">
                                        💡 Type <code class="bg-blue-100 px-1 rounded text-blue-800 font-mono font-bold">-[space] </code> before lines for bullet points
                                    </span>
                                </div>
                                <textarea id="objective" name="objective" rows="3" placeholder="e.g.,&#10;- To define guidelines for processing records...&#10;- To establish accountability metrics..." class="w-full rounded-lg border-gray-200 text-sm p-3 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-150">{{ old('objective', $doc->objective ?? '') }}</textarea>
                            </div>
                            
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
                                    <label for="scope" class="block text-xs font-bold uppercase text-gray-500 tracking-wide">Scope</label>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100/70">
                                        💡 Type <code class="bg-blue-100 px-1 rounded text-blue-800 font-mono font-bold">-[space]</code> before lines for bullet points
                                    </span>
                                </div>
                                <textarea id="scope" name="scope" rows="3" placeholder="e.g.,&#10;- Covers all internal audit operations...&#10;- Applicable to document controllers across branches..." class="w-full rounded-lg border-gray-200 text-sm p-3 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-150">{{ old('scope', $doc->scope ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="text" id="procedure-steps-json" name="procedure_steps_json" hidden />
                <div id="hidden-affected-inputs-container"></div>
            </form>

            <!-- PHASE 2: Dynamic Operational Steps Matrix Form -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md/50">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">2</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">Procedural Matrix Builder</h2>
                    </div>
                </div>
                
                <div class="p-6">
                    <form id="procedure-steps-form" class="space-y-5 bg-gray-50/50 rounded-xl border border-gray-200/60 p-5">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-4">
                                <label for="responsibility" class="block text-xs font-bold uppercase text-gray-600 mb-1.5">Responsibility</label>
                                <input type="text" id="responsibility" name="responsibility[]" placeholder="e.g., Department Head, Top Management" required class="w-full rounded-lg border-gray-200 bg-white text-sm p-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                            </div>
                            <div class="md:col-span-8">
                                <label for="activities" class="block text-xs font-bold uppercase text-gray-600 mb-1.5">Activities</label>
                                <input id="activities" name="activities[]" placeholder="State the execution action or task to be performed..." required class="w-full rounded-lg border-gray-200 bg-white text-sm p-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                            </div>
                        </div>

                        <!-- Rich Text Column -->
                        <div class="flex flex-col">
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1.5">Note / Exceptions <span class="text-gray-400 font-normal italic">(Optional)</span></label>
                            <div class="flex-1 min-h-[140px] rounded-lg border border-gray-200 bg-white overflow-hidden focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 flex flex-col">
                                <div id="note-editor" class="ql-container ql-snow flex-1 text-sm"></div>
                            </div>
                            <input type="hidden" name="note" id="note" />
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                            <!-- References Setup -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200/70 shadow-sm flex flex-col justify-between">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2.5 pb-1 border-b border-gray-100">Interfaces (References)</label>
                                    <div id="interfaces-inputs-wrapper" class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                                        @for ($i = 0; $i < 3; $i++)
                                            <div class="flex gap-1.5 items-center w-full">
                                                <select class="interface-input-category flex-1 rounded-md border-gray-200 text-xs p-1.5 bg-gray-50 focus:bg-white transition">
                                                    <option value="">Type</option>
                                                    <option value="Form">Form</option>
                                                    <option value="Procedure">Procedure</option>
                                                    <option value="MS Manual">MS Manual</option>
                                                    <option value="Support Document">Support Doc</option>
                                                    <option value="Work Instruction">Work Instruction</option>
                                                    <option value="Document">Document</option>
                                                </select>
                                                
                                                <div class="relative flex-2">
                                                    <input type="text" class="interface-input-name w-full rounded-md border-gray-200 text-xs p-1.5 placeholder-gray-300 autocomplete-interface" placeholder="Doc Code / Name #{{ $i + 1 }}" autocomplete="off"/>
                                                    <div class="autocomplete-suggestions absolute z-50 left-0 mt-1 hidden max-h-48 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-xl"></div>
                                                </div>

                                                <button type="button" class="remove-interface-btn shrink-0 text-gray-400 hover:text-red-500 text-xs px-1 font-bold transition">✖</button>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <button type="button" class="add-interface-btn mt-3 flex items-center justify-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50/50 hover:bg-blue-50 p-1.5 rounded-md border border-dashed border-blue-200 transition" data-type="input">➕ Add Reference</button>
                            </div>
                            <!-- Outputs Setup -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200/70 shadow-sm flex flex-col justify-between">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2.5 pb-1 border-b border-gray-100">Interfaces (Outputs)</label>
                                    <div id="interfaces-outputs-wrapper" class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                                        @for ($i = 0; $i < 3; $i++)
                                            <div class="flex gap-1.5 items-center w-full">
                                                <select class="interface-output-category flex-1 rounded-md border-gray-200 text-xs p-1.5 bg-gray-50 focus:bg-white transition">
                                                    <option value="">Type</option>
                                                    <option value="Form">Form</option>
                                                    <option value="Procedure">Procedure</option>
                                                    <option value="MS Manual">MS Manual</option>
                                                    <option value="Support Document">Support Doc</option>
                                                    <option value="Work Instruction">Work Instruction</option>
                                                    <option value="Document">Document</option>
                                                </select>
                                                
                                                <div class="relative flex-2">
                                                    <input type="text" class="interface-output-name w-full rounded-md border-gray-200 text-xs p-1.5 placeholder-gray-300 autocomplete-interface" placeholder="Generated Form / Record #{{ $i + 1 }}" autocomplete="off" />
                                                    <div class="autocomplete-suggestions absolute z-50 left-0 mt-1 hidden max-h-48 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-xl"></div>
                                                </div>
                                                <button type="button" class="remove-interface-btn shrink-0 text-gray-400 hover:text-red-500 text-xs px-1 font-bold transition">✖</button>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <button type="button" class="add-interface-btn mt-3 flex items-center justify-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50/50 hover:bg-emerald-50 p-1.5 rounded-md border border-dashed border-emerald-200 transition" data-type="output">➕ Add Process Output</button>
                            </div>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" id="submit-step-btn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition-all flex items-center gap-1.5">Add Step To Matrix</button>
                        </div>
                    </form>

                    <!-- Render Matrix Rows Layout -->
                    <div class="mt-6">
                        <div class="overflow-x-auto rounded-xl border border-gray-200/80 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                                    <tr>
                                        <th class="px-3 py-3.5 text-center w-12 border-r border-gray-200/60">#</th>
                                        <th class="px-4 py-3.5 text-left">Responsibility</th>
                                        <th class="px-4 py-3.5 text-left w-1/5">Activity Pipeline</th>
                                        <th class="px-4 py-3.5 text-left w-1/3">Internal Notes</th>
                                        <th class="px-4 py-3.5 text-left w-44">References</th>
                                        <th class="px-4 py-3.5 text-left w-44">Outputs</th>
                                        <th class="px-3 py-3.5 text-center w-24">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="procedure-steps-tbody" class="bg-white divide-y divide-gray-100">
                                    <tr id="noDataRow">
                                        <td colspan="7" class="text-center py-10 bg-gray-50/30">
                                            <div class="max-w-sm mx-auto flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-5">
                                                <span class="text-xl mb-1">📋</span>
                                                <p class="text-gray-400 font-medium text-xs italic">No procedural matrix entries structured yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PHASE 3: Affected Documents Tagging (Optional Impact Assessment) -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md/50">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">3</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 flex items-center gap-2">
                            Affected Documents Tagging
                            <span class="normal-case text-xs font-semibold bg-gray-200/80 text-gray-600 px-2 py-0.5 rounded-full font-sans tracking-normal">Optional</span>
                        </h2>
                    </div>
                </div>
                
                <div class="p-6 space-y-5">
                    <p class="text-xs text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200/60 leading-relaxed">
                        Affected documented information as a result of the new/revised/deleted documented information.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-slate-50/50 p-4 rounded-xl border border-slate-200/60">
                        <div class="md:col-span-8">
                            <label for="affected_doc_select" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 tracking-wide">Select Affected Master Document</label>
                            <select id="affected_doc_select" class="w-full rounded-lg border-gray-200 text-sm p-2.5 bg-white shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                                <option value="" disabled selected>-- Choose Document from Master Records --</option>
                                @if(isset($all_documents))
                                    @foreach($all_documents as $master_doc)
                                        <option value="{{ $master_doc->id }}" data-code="{{ $master_doc->code }}" data-title="{{ $master_doc->title }}">{{ $master_doc->code }} - {{ $master_doc->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <label for="affected_revision" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 tracking-wide">Revision No. <span class="text-gray-400 font-normal">(post-approval)</span></label>
                            <input type="text" id="affected_revision" placeholder="e.g., 01" class="w-full rounded-lg border-gray-200 text-sm p-2.5 bg-white shadow-sm text-center focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                        </div>
                        <div class="md:col-span-12 mt-2">
                            <label for="affected_details" class="block text-xs font-bold uppercase text-slate-700 mb-1.5 tracking-wide">Brief details on how the document was affected</label>
                            <div class="flex gap-3">
                                <input type="text" id="affected_details" placeholder="State clause updates, modified parameters, or cross-reference fixes..." class="flex-1 rounded-lg border-gray-200 text-sm p-2.5 bg-white shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                <button type="button" id="add-affected-doc-btn" class="py-2.5 px-6 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-xs uppercase tracking-wider shadow-sm transition duration-150 whitespace-nowrap">Add Row</button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-200/80 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                                <tr>
                                    <th class="px-3 py-3 text-center w-12 border-r border-gray-200/60">#</th>
                                    <th class="px-4 py-3 text-left">Document Title</th>
                                    <th class="px-4 py-3 text-left w-44">Doc No.</th>
                                    <th class="px-4 py-3 text-center w-36">Rev No.</th>
                                    <th class="px-4 py-3 text-left w-1/3">Details</th>
                                    <th class="px-4 py-3 text-center w-20">Action</th>
                                </tr>
                            </thead>
                            <tbody id="affected-docs-tbody" class="bg-white divide-y divide-gray-100">
                                <tr id="no-affected-docs-row">
                                    <td colspan="6" class="text-center py-10 bg-gray-50/30">
                                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-5">
                                            <span class="text-xl mb-1">📋</span>
                                            <p class="text-gray-400 font-medium text-xs italic">No other system documents are currently cross-referenced.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Global Action Bar -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('document.system_procedures') }}" class="px-5 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all duration-150">
                    Cancel Workflow
                </a>
                <button type="submit" class="px-8 py-3 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 shadow-md hover:shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 transition-all duration-150" form="documentForm">
                    Complete and Submit Updates
                </button>
            </div>
        </div>
    </div>

    {{-- <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script>
        let quill;
        let steps = [];
        let affectedDocs = [];
        let editingIndex = null;

        document.addEventListener('DOMContentLoaded', () => {
            quill = new Quill('#note-editor', {
                theme: 'snow',
                placeholder: 'Add regulatory reminders, specific process notes, or operational constraints...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }]
                    ]
                }
            });
            
            // Sync initial state data interfaces safely
            updateProcedureTable();
            updateAffectedDocsTable();
            updateHiddenJsonFields();
            
            // event listeners
            document.getElementById('procedure-steps-form').addEventListener('submit', function (e) {
                e.preventDefault();
                addStep();
            });
        });

        // Hydrate Steps Matrix Pipeline
        @if (old('procedure_steps_json'))
            try { steps = JSON.parse(@json(old('procedure_steps_json'))); } catch (e) { steps = []; }
        @elseif (!empty($procedureStepsJson))
            try { steps = {!! $procedureStepsJson !!}; } catch (e) { steps = []; }
        @else
            steps = [];
        @endif

        // Hydrate Affected Documents State From Existing Record or Validation Context
        @if (old('affected_docs_payload'))
            try { steps = JSON.parse(@json(old('affected_docs_payload'))); } catch (e) { affectedDocs = []; }
        @elseif (!empty($affectedDocsJson))
            try { affectedDocs = {!! is_string($affectedDocsJson) ? $affectedDocsJson : json_encode($affectedDocsJson) !!}; } catch (e) { affectedDocs = []; }
        @else
            affectedDocs = [];
        @endif

        // Handling procedure steps matrix
        // Helper function for retrieving interfaces
        function getInterfaceDataFromWrapper(wrapperId) {
            const items = [];
            document.querySelectorAll(`#${wrapperId} .flex`).forEach(row => {
                const category = row.querySelector('[class*="category"]')?.value.trim();
                const name = row.querySelector('[class*="name"]')?.value.trim();
                if (category || name) {
                    items.push({ category, name });
                }
            });
            return items;
        }

        function addStep() {
            let noteHtml = quill.getText().trim() === '' ? null : quill.root.innerHTML;
            document.getElementById('note').value = noteHtml;

            const step = {
                responsibility: document.getElementById('responsibility').value.trim(),
                activities: document.getElementById('activities').value.trim(),
                note: noteHtml,
                interfaces_input: getInterfaceDataFromWrapper('interfaces-inputs-wrapper'),
                interfaces_output: getInterfaceDataFromWrapper('interfaces-outputs-wrapper')
            };

            if (editingIndex !== null) {
                steps[editingIndex] = step;
                editingIndex = null;
                document.getElementById('submit-step-btn').innerText = "Add Step To Matrix";
            } else {
                steps.push(step);
            }

            updateProcedureTable();
            updateHiddenJsonFields();
            clearStepsForm();
        };

        function updateProcedureTable() {
            const tbody = document.getElementById('procedure-steps-tbody');
            if (!tbody) return;

            tbody.innerHTML = '';

            if (steps.length === 0) {
                tbody.innerHTML = `
                    <tr id="noDataRow">
                        <td colspan="7" class="text-center py-10 bg-gray-50/30">
                            <div class="max-w-sm mx-auto flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-5">
                                <span class="text-xl mb-1">📋</span>
                                <p class="text-gray-400 font-medium text-xs italic">No procedural matrix entries structured yet.</p>
                            </div>
                        </td>
                    </tr>`;
                return;
            }

            steps.forEach((item, index) => {
                let inputHTML = '';
                let outputHTML = '';

                item.interfaces_input?.forEach(ref => {
                    if (ref && ref.name) inputHTML += `<div class="items-center gap-1 bg-indigo-50/60 text-indigo-700 text-[11px] font-semibold px-2 py-1 rounded border border-indigo-100/80 mb-1.5 max-w-[180px] whitespace-normal break-words"><span class="text-blue-600 font-bold">[${ref.category || 'Doc'}]</span> ${ref.name}</div>`;
                });
                inputHTML = inputHTML || '<span class="text-gray-300 italic text-xs">None</span>';

                item.interfaces_output?.forEach(out => {
                    if (out && out.name) outputHTML += `<div class="items-center gap-1 bg-emerald-50/60 text-emerald-700 text-[11px] font-semibold px-2 py-1 rounded border border-emerald-100/80 mb-1.5 max-w-[180px] whitespace-normal break-words"><span class="text-emerald-600 font-bold">[${out.category || 'Doc'}]</span> ${out.name}</div><br>`;
                });
                outputHTML = outputHTML || '<span class="text-gray-300 italic text-xs">None</span>';

                const row = document.createElement('tr');
                row.className = "hover:bg-slate-50/40 transition duration-150 align-top";
                row.innerHTML = `
                    <td class="py-3 px-4 text-xs font-bold text-gray-500">
                        <div class="flex items-center gap-1">
                            <span class="w-4 text-center">${index + 1}</span>
                            <div class="flex flex-col">
                                <button type="button" onclick="moveStepUp(${index})" class="p-0.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition disabled:opacity-20" ${index === 0 ? 'disabled' : ''}><i class="fa-solid fa-square-caret-up"></i></button>
                                <button type="button" onclick="moveStepDown(${index})" class="p-0.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition disabled:opacity-20" ${index === steps.length - 1 ? 'disabled' : ''}><i class="fa-solid fa-square-caret-down"></i></button>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-gray-900 font-bold tracking-tight">${item.responsibility}</td>
                    <td class="px-4 py-3.5 text-gray-700 leading-relaxed whitespace-normal break-words">${item.activities}</td>
                    <td class="px-4 py-3.5 text-gray-600 prose prose-sm max-w-none leading-normal">${item.note || '<span class="text-gray-300 italic text-xs">No execution notes defined</span>'}</td>
                    <td class="px-4 py-3.5 space-y-1">${inputHTML}</td>
                    <td class="px-4 py-3.5 space-y-1">${outputHTML}</td>
                    <td class="px-3 py-3.5 text-center space-x-1 whitespace-nowrap">
                        <button type="button" class="inline-flex items-center justify-center border border-gray-200 w-7 h-7 rounded-lg bg-white shadow-sm text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50/30 transition duration-150" onclick="editStep(${index})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="inline-flex items-center justify-center border border-gray-200 w-7 h-7 rounded-lg bg-white shadow-sm text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50/30 transition duration-150" onclick="removeStep(${index})" title="Remove"><i class="fa-solid fa-xmark"></i></button>
                    </td>`;
                tbody.appendChild(row);
            });
        }
        // -------- End of handling procedure step matrix

        // Shifts a procedure step up
        function moveStepUp(index) {
            if (index <= 0) return;

            // Atomic element item swap execution
            [steps[index - 1], steps[index]] = [steps[index], steps[index - 1]];

            finalizeSequenceUpdate();
        }

        // Shifts a procedure step down
        function moveStepDown(index) {
            if (index >= steps.length - 1) return;

            // Atomic element item swap execution
            [steps[index], steps[index + 1]] = [steps[index + 1], steps[index]];

            finalizeSequenceUpdate();
        }

        function finalizeSequenceUpdate() {
            updateProcedureTable();
            updateHiddenJsonFields();
        }

        function editStep(index) {
            const selectedStep = steps[index];
            editingIndex = index;

            document.getElementById('responsibility').value = selectedStep.responsibility;
            document.getElementById('activities').value = selectedStep.activities;
            document.getElementById('note').value = selectedStep.note;
            quill.root.innerHTML = selectedStep.note || '';

            const inputRows = document.querySelectorAll('#interfaces-inputs-wrapper');
            inputRows.forEach((row, i) => {
                const cat = row.querySelector('.interface-input-category');
                const name = row.querySelector('.interface-input-name');

                if (selectedStep.interfaces_input?.[i]) {
                    cat.value = selectedStep.interfaces_input[i].category || '';
                    name.value = selectedStep.interfaces_input[i].name || '';
                } else {
                    cat.value = '';
                    name.value = '';
                }
            });

            const outputRows = document.querySelectorAll('#interfaces-outputs-wrapper');
            outputRows.forEach((row, i) => {
                const cat = row.querySelector('.interface-output-category');
                const name = row.querySelector('.interface-output-name');

                if (selectedStep.interfaces_output?.[i]) {
                    cat.value = selectedStep.interfaces_output[i].category || '';
                    name.value = selectedStep.interfaces_output[i].name || '';
                } else {
                    cat.value = '';
                    name.value = '';
                }
            });
            
            document.getElementById('submit-step-btn').innerText = "Update Matrix Step";
            document.getElementById('procedure-steps-form').scrollIntoView({ behavior: 'smooth' });
        }

        function removeStep(index) {
            steps.splice(index, 1);
            updateProcedureTable();
            updateHiddenJsonFields();
        }

        function clearStepsForm() {
            document.getElementById('procedure-steps-form').reset();
            quill.setContents([]);
        }

        // ================= PHASE 3: AFFECTED DOCUMENTS LOGIC =================
        document.getElementById('add-affected-doc-btn').addEventListener('click', () => {
            const selectEl = document.getElementById('affected_doc_select');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const revInput = document.getElementById('affected_revision');
            const detailsInput = document.getElementById('affected_details');

            if (!selectEl.value) {
                alert('Please select a master document standard.');
                return;
            }

            const docId = selectEl.value;
            const docCode = selectedOption.dataset.code;
            const docTitle = selectedOption.dataset.title;
            const revision = revInput.value.trim() || '00';
            const details = detailsInput.value.trim();

            if (!details) {
                alert('Please add execution details regarding the modification scope.');
                return;
            }

            // Prevent duplicating rows for same document reference identifier
            if (affectedDocs.some(d => d.document_id == docId)) {
                alert('This document is already cross-referenced in the dynamic impact list.');
                return;
            }

            affectedDocs.push({
                document_id: docId,
                code: docCode,
                title: docTitle,
                revision_number: revision,
                details: details
            });

            updateAffectedDocsTable();
            updateHiddenJsonFields();

            // Reset inputs
            selectEl.value = "";
            revInput.value = "";
            detailsInput.value = "";
        });

        function updateAffectedDocsTable() {
            const tbody = document.getElementById('affected-docs-tbody');
            if (!tbody) return;
            tbody.innerHTML = '';
            
            if (affectedDocs.length === 0) {
                tbody.innerHTML = `
                    <tr id="no-affected-docs-row">
                        <td colspan="6" class="text-center py-10 bg-gray-50/30">
                            <div class="max-w-sm mx-auto flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-5">
                                <span class="text-xl mb-1">📋</span>
                                <p class="text-gray-400 font-medium text-xs italic">No other system documents are currently cross-referenced.</p>
                            </div>
                        </td>
                    </tr>`;
                return;
            }

            affectedDocs.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = "hover:bg-slate-50/50 transition duration-150 align-top";
                row.innerHTML = `
                    <td class="px-3 py-3.5 text-center text-gray-400 font-bold border-r border-gray-100">${index + 1}</td>
                    <td class="px-4 py-3.5 text-gray-900 font-semibold tracking-tight whitespace-normal break-words">${item.title}</td>
                    <td class="px-4 py-3.5 text-gray-700 font-mono font-bold">${item.code}</td>
                    <td class="px-4 py-3.5 text-center font-medium text-gray-700">${item.revision_number || '<span class="text-gray-300 italic font-normal text-xs">None</span>'}</td>
                    <td class="px-4 py-3.5 text-gray-600 whitespace-normal break-words leading-relaxed">${item.details || '<span class="text-gray-300 italic text-xs">No modification constraints specified</span>'}</td>
                    <td class="px-4 py-3.5 text-center">
                        <button type="button" class="text-gray-400 hover:text-red-500 p-1 rounded-md hover:bg-gray-100 transition text-sm" onclick="removeAffectedDoc(${index})" title="Remove"><i class="fa-solid fa-xmark"></i></button>
                    </td>`;
                tbody.appendChild(row);
            });
        }

        function removeAffectedDoc(index) {
            affectedDocs.splice(index, 1);
            updateAffectedDocsTable();
            updateHiddenJsonFields();
        }

        // ================= GLOBAL STATE BOUNDARY STORAGE HANDLING =================
        function updateHiddenJsonFields() {
            // Master operational builder json pipeline
            document.getElementById('procedure-steps-json').value = JSON.stringify(steps);

            // Phase 3 dynamic payload fields generated sequentially for array submit validation inside Request lifecycle
            const container = document.getElementById('hidden-affected-inputs-container');
            container.innerHTML = '';

            affectedDocs.forEach((item, index) => {
                container.innerHTML += `
                    <input type="hidden" name="affected_docs_payload[${index}][document_id]" value="${item.document_id}" />
                    <input type="hidden" name="affected_docs_payload[${index}][revision_number]" value="${item.revision_number}" />
                    <input type="hidden" name="affected_docs_payload[${index}][details]" value="${item.details}" />
                `;
            });
        }

        document.addEventListener('click', function (e) {
            const addBtn = e.target.closest('.add-interface-btn');
            if (addBtn) {
                const type = addBtn.dataset.type;
                const wrapper = type === 'input' ? document.getElementById('interfaces-inputs-wrapper') : document.getElementById('interfaces-outputs-wrapper');
                const index = wrapper.children.length + 1;
                const prefix = type === 'input' ? 'input' : 'output';
                const label = type === 'input' ? 'Doc Code / Name' : 'Generated Form / Record';

                const row = document.createElement('div');
                row.className = 'flex gap-1.5 items-center';
                row.innerHTML = `
                    <select class="interface-${prefix}-category flex-1 rounded-md border-gray-200 text-xs p-1.5 bg-gray-50 focus:bg-white transition">
                        <option value="">Type</option>
                        <option value="Form">Form</option>
                        <option value="Procedure">Procedure</option>
                        <option value="MS Manual">MS Manual</option>
                        <option value="Support Document">Support Doc</option>
                        <option value="Work Instruction">Work Instruction</option>
                        <option value="Document">Document</option>
                    </select>
                    
                    <div class="relative flex-2">
                        <input type="text" class="interface-${prefix}-name w-full rounded-md border-gray-200 text-xs p-1.5 placeholder-gray-300 autocomplete-interface" placeholder="${label} #${index}" autocomplete="off" />
                        <div class="autocomplete-suggestions absolute z-50 left-0 mt-1 hidden max-h-48 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-xl"></div>
                    </div>

                    <button type="button" class="remove-interface-btn shrink-0 text-gray-400 hover:text-red-500 text-xs px-1 font-bold transition">✖</button>
                `;

                wrapper.appendChild(row);
                return;
            }

            const removeBtn = e.target.closest('.remove-interface-btn');
            if (removeBtn) removeBtn.parentElement.remove();
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Pass down the independent historical arrays from your controller
            // If these are raw string arrays (e.g., ["Form-01", "Log Sheet"]), this maps perfectly.
            const inputsPool = @json($existing_inputs ?? []);
            const outputsPool = @json($existing_outputs ?? []);
            
            // 2. Pair each wrapper with its dedicated historical search pool
            const autocompleteConfigs = [
                {
                    wrapper: document.getElementById('interfaces-inputs-wrapper'),
                    pool: inputsPool
                },
                {
                    wrapper: document.getElementById('interfaces-outputs-wrapper'),
                    pool: outputsPool
                }
            ];

            autocompleteConfigs.forEach(config => {
                if (!config.wrapper) return;

                // Use event delegation to catch interactions across standard or dynamically added rows
                config.wrapper.addEventListener('input', function(e) {
                    if (!e.target.classList.contains('autocomplete-interface')) return;

                    const inputField = e.target;
                    const suggestionBox = inputField.nextElementSibling;
                    const query = inputField.value.toLowerCase().trim();

                    // Clear suggestion layout instantly
                    suggestionBox.innerHTML = '';

                    if (query.length < 2) {
                        suggestionBox.classList.add('hidden');
                        return;
                    }

                    // Filter against the specific paired target pool array
                    // Handles both raw strings or objects with a 'title'/'name' property gracefully
                    const results = config.pool.filter(item => {
                        const searchString = typeof item === 'string' ? item : (item.title || item.name || '');
                        return searchString.toLowerCase().includes(query);
                    }).slice(0, 6); // Keep dropdown height elegant

                    if (results.length === 0) {
                        suggestionBox.classList.add('hidden');
                        return;
                    }

                    // Generate layout markup list options
                    results.forEach(item => {
                        const displayValue = typeof item === 'string' ? item : (item.title || item.name);
                        
                        const listRow = document.createElement('div');
                        listRow.className = 'px-3 py-1.5 text-xs text-gray-700 hover:bg-slate-50 cursor-pointer border-b border-gray-50 last:border-b-0 transition-colors duration-150';
                        listRow.textContent = displayValue;

                        // Handle suggestion click selection
                        listRow.addEventListener('click', () => {
                            inputField.value = displayValue;
                            suggestionBox.classList.add('hidden');
                            
                            // Dispatch change event to bubble updates cleanly
                            inputField.dispatchEvent(new Event('change', { bubbles: true }));
                        });

                        suggestionBox.appendChild(listRow);
                    });

                    suggestionBox.classList.remove('hidden');
                });
            });

            // Globally collapse open option boxes when users focus elsewhere
            document.addEventListener('click', function(e) {
                if (!e.target.classList.contains('autocomplete-interface')) {
                    document.querySelectorAll('.autocomplete-suggestions').forEach(box => {
                        box.classList.add('hidden');
                    });
                }
            });
        });
    </script>
</x-layout>