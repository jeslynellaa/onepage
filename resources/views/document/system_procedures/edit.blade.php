<x-layout>
    <style>
        input:read-only {
            color: gray;
            background: lightgray;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > System Procedures > Edit Existing Procedures
        </h1>

        <div class="shadow-md rounded-lg bg-white p-5 mt-2">
            <form id="documentForm" method="POST"
                action="{{
                    isset($doc) && $doc->status !== 'Active'
                    ? route('document.system_procedures.update', $doc->id)
                    : route('document.system_procedures.store') }}" 
                    enctype="multipart/form-data" class="space-y-6"
            >
                @csrf
                @if(isset($doc) && $doc->status !== 'Active')
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-6">
                        <label for="title" class="block text-xs font-bold uppercase mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') ?? $doc->title }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                        <input type="hidden" name="type" value="System Procedures">
                    </div>

                    <div class="md:col-span-3">
                        <label for="section_id" class="block text-xs font-bold uppercase mb-1">Section No.</label>
                            
                        <select name="section_id" id="section_id" value="{{ $doc->section_id }}" class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                            <option disabled>-- Select --</option>
                            <option value="1" @if($doc->section_id == '1') echo selected @else echo disabled @endif>01 Business Planning</option>
                            <option value="2" @if($doc->section_id == '2') echo selected @else echo disabled @endif>02 Business Development</option>
                            <option value="3" @if($doc->section_id == '3') echo selected @else echo disabled @endif>03 Project Planning and Implementation</option>
                            <option value="4" @if($doc->section_id == '4') echo selected @else echo disabled @endif>04 Project Evaluation</option>
                            <option value="5" @if($doc->section_id == '5') echo selected @else echo disabled @endif>05 Project Completion</option>
                            <option value="6" @if($doc->section_id == '6') echo selected @else echo disabled @endif>06 Asset Management</option>
                            <option value="7" @if($doc->section_id == '7') echo selected @else echo disabled @endif>07 Maintenance</option>
                            <option value="8" @if($doc->section_id == '8') echo selected @else echo disabled @endif>08 Human Resource Management</option>
                            <option value="9" @if($doc->section_id == '9') echo selected @else echo disabled @endif>09 Financial Resource Management</option>
                            <option value="10" @if($doc->section_id == '10') echo selected @else echo disabled @endif>10 Documented Information Management</option>
                            <option value="11" @if($doc->section_id == '11') echo selected @else echo disabled @endif>11 Continual Improvement</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label for="code" class="block text-xs font-bold uppercase mb-1">Code</label>
                        <input type="text" readonly id="code" name="code" value="{{ old('code') ?? $doc->code}}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>
                </div>
                

                <div class="col-span-full">
                    <label for="justification" class="block text-xs font-bold uppercase mb-1">Justification/ Objective</label>
                    <input type="text" id="justification" name="justification" placeholder="Explain why document is created or modified" value="{{ isset($doc) && $doc->status !== 'Active'
                        ? old('justification', $doc->justification)
                        : old('justification') }}" 
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                </div>
                
                <hr class="border-gray-300">
                
                <!-- Objective & Scope -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="objective" class="block text-xs font-bold uppercase mb-1">Objective</label>
                        <textarea id="objective" name="objective" rows="5"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">{{ old('objective') ?? $doc->objective }}</textarea>
                    </div>
                    <div>
                        <label for="scope" class="block text-xs font-bold uppercase mb-1">Scope</label>
                        <textarea id="scope" name="scope" rows="5"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">{{ old('scope') ?? $doc->scope }}</textarea>
                    </div>
                </div>
                <input type="text" id="procedure-steps-json" name="procedure_steps_json" hidden />
            </form>

            <!-- ========== STEPS FORM ========== -->
            <form id="procedure-steps-form" class="space-y-6">
                <hr class="border-gray-300">
                <p class="text-center font-bold text-gray-700 uppercase">Steps</p>
                <!-- Responsibility & Activities -->
                <div class="flex flex-row w-full gap-4">
                    <div class="w-1/3">
                        <label for="responsibility" class="block text-xs font-bold uppercase mb-1">Responsibility</label>
                        <input type="text" id="responsibility" name="responsibility[]" required
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>
                    <div class="w-2/3">
                        <label for="activities" class="block text-xs font-bold uppercase mb-1">Activities</label>
                        <input id="activities" name="activities[]" required
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Notes -->
                    <div class="relative w-full h-60 rounded-md">
                        <label class="block text-xs font-bold uppercase mb-1">Include a Note (optional)</label>
                        <div id="note-editor" class="ql-container ql-snow h-4/6!"></div>
                        <input type="hidden" name="note" id="note">
                    </div>

                    <!-- Interfaces -->
                    <div>
                        <label for="interfaces_input" class="block text-xs font-bold uppercase mb-1">Interfaces (References)</label>
                        <div id="interfaces-inputs-wrapper" class="space-y-2">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="flex gap-2">
                                    <select
                                        class="interface-input-category w-1/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                                        <option value="">Select category</option>
                                        <option value="Form">Form</option>
                                        <option value="Procedure">System Procedure</option>
                                        <option value="MS Manual">MS Manual</option>
                                        <option value="Support Document">Support Document</option>
                                        <option value="Work Instruction">Work Instruction</option>
                                        <option value="Document">Document</option>
                                    </select>
                                    <input type="text"
                                        class="interface-input-name w-2/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"
                                        placeholder="Reference #{{ $i + 1 }}">
                                    <button type="button" class="remove-interface-btn text-red-500 hover:text-red-700 text-sm font-bold">✖</button>
                                </div>
                            @endfor
                        </div>
                        <button type="button" class="add-interface-btn mt-2 flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800" data-type="input">➕ Add Interface</button>
                    </div>

                    <div>
                        <label for="interfaces_output" class="block text-xs font-bold uppercase mb-1">Interfaces (Outputs)</label>
                        <div id="interfaces-outputs-wrapper" class="space-y-2">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="flex gap-2">
                                    <select
                                        class="interface-output-category w-1/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                                        <option value="">Select category</option>
                                        <option value="Form">Form</option>
                                        <option value="Procedure">System Procedure</option>
                                        <option value="MS Manual">MS Manual</option>
                                        <option value="Support Document">Support Document</option>
                                        <option value="Work Instruction">Work Instruction</option>
                                        <option value="Document">Document</option>
                                    </select>
                                    <input type="text"
                                        class="interface-output-name w-2/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus-border-blue-500 text-sm p-2"
                                        placeholder="Output #{{ $i + 1 }}">
                                    <button type="button" class="remove-interface-btn text-red-500 hover:text-red-700 text-sm font-bold">✖</button>
                                </div>
                            @endfor
                        </div>
                        <button type="button" class="add-interface-btn mt-2 flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800" data-type="output">➕ Add Interface</button>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Add Step</button>
                </div>
            </form>

            <!-- ========== TABLE ========== -->
            <div class="overflow-x-auto mt-6" id="procedure-steps-table">
                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="border">#</th>
                            <th class="border px-3 py-2">Responsibility</th>
                            <th class="border px-3 py-2">Actions</th>
                            <th class="border px-3 py-2">Note</th>
                            <th class="border px-3 py-2">Interfaces (References)</th>
                            <th class="border px-3 py-2">Interfaces (Outputs)</th>
                            <th class="border px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="procedure-steps-table">
                        <tr id="noDataRow">
                            <td colspan="6" class="text-center py-3 text-gray-500">No Procedure Steps Added</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-6 mb-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition" form="documentForm">
                    Submit
                </button>
            </div>
        </div>
    </div>

    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        let steps = [];
        let quill; // declare globally so all functions can access it

        document.addEventListener('DOMContentLoaded', () => {
            quill = new Quill('#note-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }]
                    ]
                }
            });
        });

        @if (old('procedure_steps_json'))
            // Validation failed — restore user-edited steps
            try {
                steps = JSON.parse(@json(old('procedure_steps_json')));
            } catch (e) {
                steps = [];
            }
        @elseif (!empty($procedureStepsJson))
            steps = {!! $procedureStepsJson !!};
        @else
            steps = [];
        @endif
        console.log(steps);
        let editingIndex = null;

        // Populate the table initially
        document.addEventListener('DOMContentLoaded', function () {
            updateProcedureTable();
            updateHiddenJsonField();
        });

        // HANDLE EXPENSE DETAILS FUNCTIONALITY
        document.getElementById('procedure-steps-form').addEventListener('submit', function (e) {
            e.preventDefault();
            addStep();
        });

        function addStep() {
            let noteHtml = quill.getText().trim() === '' 
                ? null 
                : quill.root.innerHTML;

            document.getElementById('note').value = noteHtml;

            const inputs = [];
            const outputs = [];

            // Collect References (Inputs)
            document.querySelectorAll('#interfaces-inputs-wrapper .flex').forEach(row => {
                const category = row.querySelector('.interface-input-category')?.value.trim();
                const name = row.querySelector('.interface-input-name')?.value.trim();
                if (category || name) {
                    inputs.push({ category, name });
                }
            });

            // Collect Outputs
            document.querySelectorAll('#interfaces-outputs-wrapper .flex').forEach(row => {
                const category = row.querySelector('.interface-output-category')?.value.trim();
                const name = row.querySelector('.interface-output-name')?.value.trim();
                if (category || name) {
                    outputs.push({ category, name });
                }
            });

            const step = {
                responsibility: document.getElementById('responsibility').value,
                activities: document.getElementById('activities').value,
                note: noteHtml,
                interfaces_input: inputs,
                interfaces_output: outputs
            };

            if (editingIndex !== null) { // if editing
                steps[editingIndex] = step;
                editingIndex = null; // reset edit mode
            } else {
                steps.push(step);   // if adding new
            }

            updateProcedureTable();
            updateHiddenJsonField();
            clearStepsForm();
        }

        function updateProcedureTable() {
            const tbody = document.querySelector('#procedure-steps-table tbody');
            tbody.innerHTML = '';

            if(steps.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="6" class="text-center py-3 text-gray-500">No Procedure Steps Added</td>`;
                tbody.appendChild(row);
            }else{
                steps.forEach((item, index) => {
                    let inputHTML = '';
                    let outputHTML = '';

                    // Loop through interfaces_input
                    item.interfaces_input?.forEach((ref, i) => {
                        const title = typeof ref === 'object' ? ref.name : ref;
                        inputHTML += `${title}<br>`;
                    });

                    // Loop through interfaces_output
                    (Array.isArray(item.interfaces_output) ? item.interfaces_output : [])
                        .forEach((out, i) => {
                        const title = typeof out === 'object' ? out.name : out;
                        outputHTML += `${title}<br>`;
                    });

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border border-gray-300 px-3 py-2 align-top">${index+1}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${item.responsibility}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${item.activities}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${item.note || '-'}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${inputHTML || '-'}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${outputHTML || '-'}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top text-center">
                            <button type="button" title="Remove" class="border w-8 rounded-3xl p-2 cursor-pointer transition-colors duration-300 hover:text-sky-700" onclick="removeStep(${index})"><i class="fa-solid fa-xmark"></i></button>
                            <button type="button" title="Edit" class="border w-8 rounded-3xl p-2 cursor-pointer transition-colors duration-300 hover:text-sky-700" onclick="editStep(${index})"><i class="fa-solid fa-pen"></i></button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        }

        function updateHiddenJsonField() {
            document.getElementById('procedure-steps-json').value = JSON.stringify(steps);
        }

        function clearStepsForm() {
            document.getElementById('procedure-steps-form').reset();
            quill.setContents([]);
        }

        function removeStep(index) {
            steps.splice(index, 1);
            updateProcedureTable();
            updateHiddenJsonField();
        }

        function editStep(index) {
            const selectedStep = steps[index];
            editingIndex = index;
            console.log(selectedStep);

            // Remove from array temporarily
            // steps.splice(index, 1);
            updateProcedureTable();
            updateHiddenJsonField();

            // Populate input fields
            $('#responsibility').val(selectedStep.responsibility);
            $('#activities').val(selectedStep.activities);
            $('#note').val(selectedStep.note);
            quill.root.innerHTML = selectedStep.note || '';

            // Populate INTERFACE INPUTS
            const inputRows = document.querySelectorAll('#interfaces-inputs-wrapper .flex');
            inputRows.forEach((row, i) => {
                const categorySelect = row.querySelector('.interface-input-category');
                const nameInput = row.querySelector('.interface-input-name');

                if (selectedStep.interfaces_input && selectedStep.interfaces_input[i]) {
                    const { category, name } = selectedStep.interfaces_input[i];
                    categorySelect.value = category || '';
                    nameInput.value = name || '';
                } else {
                    categorySelect.value = '';
                    nameInput.value = '';
                }
            });

            // Populate INTERFACE OUTPUTS
            const outputRows = document.querySelectorAll('#interfaces-outputs-wrapper .flex');
            outputRows.forEach((row, i) => {
                const categorySelect = row.querySelector('.interface-output-category');
                const nameInput = row.querySelector('.interface-output-name');

                if (selectedStep.interfaces_output && selectedStep.interfaces_output[i]) {
                    const { category, name } = selectedStep.interfaces_output[i];
                    categorySelect.value = category || '';
                    nameInput.value = name || '';
                } else {
                    categorySelect.value = '';
                    nameInput.value = '';
                }
            });
            
            document.getElementById('procedure-steps-form').scrollIntoView({ behavior: 'smooth' });
        }
        
        document.addEventListener('click', function (e) {
            const addBtn = e.target.closest('.add-interface-btn');
            if (addBtn){
                const type = addBtn.dataset.type;
                const wrapper = type === 'input' ? document.getElementById('interfaces-inputs-wrapper') : document.getElementById('interfaces-outputs-wrapper');
                const index = wrapper.children.length + 1;

                const row = document.createElement('div');
                row.className = 'flex gap-2';

                row.innerHTML = '<select class="interface-' + (type === 'input' ? 'input' : 'output') + '-category w-1/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"><option value="">Select category</option><option value="Form">Form</option><option value="Procedure">System Procedure</option><option value="MS Manual">MS Manual</option><option value="Support Document">Support Document</option><option value="Work Instruction">Work Instruction</option><option value="Document">Document</option></select><input type="text" class="interface-' + (type === 'input' ? 'input' : 'output') + '-name w-2/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" placeholder="' + (type === 'input' ? 'Reference' : 'Output') + ' #' + index + '"><button type="button" class="remove-interface-btn text-red-500 hover:text-red-700 text-sm font-bold">✖</button>';

                wrapper.appendChild(row);
                return;
            }

            const removeBtn = e.target.closest('.remove-interface-btn');
            if (removeBtn) removeBtn.parentElement.remove();
        });
    </script>
</x-layout>