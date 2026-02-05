<x-layout>
    @php
        function selected($value, $old) {
            return $value == $old ? 'selected' : '';
        }
    @endphp

    <style>
        input:read-only {
            color: gray;
            background: lightgray;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > System Procedures > {{$doc->title}} > Comment
        </h1>

        <div class="flex flex-col sm:flex-row w-full gap-4">
            <div id="preview-container" class="w-full md:w-1/2 h-140">
                <iframe src="{{ route('document.system_procedures.sp_preview', $doc->id) }}" class="w-full h-full"></iframe>
            </div>
            <div id="form-container" class="w-full md:w-1/2 h-140">
                <div class="px-5 py-4 w-full border rounded-md">
                    <span>
                        Write comments regarding each section
                    </span>
                    <hr>
                    <div>
                        <form action="" class="py-2">
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="title_comment">Title</label>
                                <input type="text" name="title_comment" >
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="objectives_comment">Objective/s</label>
                                <input type="text" name="objectives_comment">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="scope_comment">Scope</label>
                                <input type="text" name="scope_comment">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="flowchart_comments">Process Flowchart</label>
                                <input type="text" name="flowchart_comments">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="note_comments">Notes</label>
                                <input type="text" name="note_comments">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="interfaces_comments">Interfaces</label>
                                <input type="text" name="interfaces_comments">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="remarks">Other Remarks</label>
                                <input type="text" name="remarks">
                            </div>
                            <div class="flex justify-center gap-4">
                                <button type="submit" title="Submit and Send Back" class="bg-blue-300 rounded-xl px-3 py-1">Save</button>
                                <button class="bg-blue-300 rounded-xl px-3 py-1">
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
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

        // HANDLE EXPENSE DETAILS FUNCTIONALITY
        let steps = [];
        let editingIndex = null;

        // 🔁 Restore steps from old input (if validation failed)
        @if (old('procedure_steps_json'))
            try {
                steps = JSON.parse(@json(old('procedure_steps_json')));
                updateProcedureTable();
                updateHiddenJsonField();
            } catch (e) {
                steps = [];
            }
        @endif

        document.getElementById('procedure-steps-form').addEventListener('submit', function (e) {
            e.preventDefault();
            addStep();
        });

        function addStep() {
            document.getElementById('note').value = quill.root.innerHTML;
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
                console.log()
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
                    console.log(item.interfaces_output)
                    let inputHTML = '';
                    let outputHTML = '';

                    // Loop through interfaces_input
                    item.interfaces_input?.forEach((ref, i) => {
                        const title = typeof ref === 'object' ? ref.name : ref;
                        inputHTML += `${title}<br>`;
                    });

                    // Loop through interfaces_output
                    item.interfaces_output?.forEach((out, i) => {
                        const title = typeof out === 'object' ? out.name : out;
                        outputHTML += `${title}<br>`;
                    });

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border border-gray-300 px-3 py-2 align-top">${index+1}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${item.responsibility}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${item.activities}</td>
                        <td class="border border-gray-300 px-3 py-2 align-top">${item.note}</td>
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
            const btn = e.target.closest('.add-interface-btn');
            if (!btn) return;

            const type = btn.dataset.type;
            const wrapper = type === 'input' ? document.getElementById('interfaces-inputs-wrapper') : document.getElementById('interfaces-outputs-wrapper');
            const index = wrapper.children.length + 1;

            const row = document.createElement('div');
            row.className = 'flex gap-2';

            row.innerHTML = '<select class="interface-' + (type === 'input' ? 'input' : 'output') + '-category w-1/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"><option value="">Select category</option><option value="Form">Form</option><option value="Procedure">System Procedure</option><option value="MS Manual">MS Manual</option><option value="Support Document">Support Document</option><option value="Work Instruction">Work Instruction</option><option value="Document">Document</option></select><input type="text" class="interface-' + (type === 'input' ? 'input' : 'output') + '-name w-2/3 rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" placeholder="' + (type === 'input' ? 'Reference' : 'Output') + ' #' + index + '">';

            wrapper.appendChild(row);
        });
    </script>
</x-layout>