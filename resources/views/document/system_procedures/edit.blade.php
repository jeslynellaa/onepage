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
                        <label for="section_number" class="block text-xs font-bold uppercase mb-1">Section No.</label>
                            
                        <select name="section_number" id="section_number" value="{{ $doc->section_number }}" class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                            <option disabled>-- Select --</option>
                            <option value="01" @if($doc->section_number === '01') echo selected @else echo disabled @endif>01 Business Planning</option>
                            <option value="02" @if($doc->section_number === '02') echo selected @else echo disabled @endif>02 Business Development</option>
                            <option value="03" @if($doc->section_number === '03') echo selected @else echo disabled @endif>03 Project Planning and Implementation</option>
                            <option value="04" @if($doc->section_number === '04') echo selected @else echo disabled @endif>04 Project Evaluation</option>
                            <option value="05" @if($doc->section_number === '05') echo selected @else echo disabled @endif>05 Project Completion</option>
                            <option value="06" @if($doc->section_number === '06') echo selected @else echo disabled @endif>06 Asset Management</option>
                            <option value="07" @if($doc->section_number === '07') echo selected @else echo disabled @endif>07 Maintenance</option>
                            <option value="08" @if($doc->section_number === '08') echo selected @else echo disabled @endif>08 Human Resource Management</option>
                            <option value="09" @if($doc->section_number === '09') echo selected @else echo disabled @endif>09 Financial Resource Management</option>
                            <option value="10" @if($doc->section_number === '10') echo selected @else echo disabled @endif>10 Documented Information Management</option>
                            <option value="11" @if($doc->section_number === '11') echo selected @else echo disabled @endif>11 Continual Improvement</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label for="code" class="block text-xs font-bold uppercase mb-1">Code</label>
                        <input type="text" readonly id="code" name="code" value="{{ old('code') ?? $doc->code}}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>
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
                    <div>
                        <label for="note" class="block text-xs font-bold uppercase mb-1">Include a Note (optional)</label>
                        <textarea id="note" name="note[]" rows="10"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"></textarea>
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
                                </div>
                            @endfor
                        </div>
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
                                </div>
                            @endfor
                        </div>
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

    <script>
        let steps = [];

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
                note: document.getElementById('note').value,
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
    </script>
</x-layout>