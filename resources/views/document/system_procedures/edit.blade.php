<x-layout>
    <div class="mx-auto my-3 w-3/4 p-5 shadow-md rounded-lg bg-white">
        <h3 class="text-center font-weight-bold">Edit an Existing System Procedure</h3>
    
        <!-- ERROR NOTIFS -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if($errors)
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        @endif

        <form id="documentForm" method="POST" action="{{ route('document.system_procedures.update', $doc->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-12">
                    <label for="title" class="block text-xs font-bold uppercase mb-1">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') ?? $doc->title}}"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    <input type="hidden" name="type" value="System Procedures">
                </div>

                <div class="md:col-span-3">
                    <label for="code" class="block text-xs font-bold uppercase mb-1">Code</label>
                    <input type="text" id="code" name="code" value="{{ old('code') ?? $doc->code }}"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                </div>

                <div class="md:col-span-3">
                    <label for="section_number" class="block text-xs font-bold uppercase mb-1">Section No.</label>
                    <input type="text" id="section_number" name="section_number" value="{{ old('section_number') ?? $doc->section_number }}"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                </div>

                <div class="md:col-span-3">
                    <label for="revision_number" class="block text-xs font-bold uppercase mb-1">Revision No.</label>
                    <input type="text" id="revision_number" name="revision_number" value="{{ old('revision_number') ?? $doc->revision_number }}"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                </div>

                <div class="md:col-span-3">
                    <label for="effective_date" class="block text-xs font-bold uppercase mb-1">Effective Date</label>
                    <input type="date" id="effective_date" name="effective_date" value="{{ old('effective_date') ?? $doc->effective_date }}"
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

            <input type="hidden" name="procedure_steps_json" id="procedure-steps-json">
        </form>

        <!-- ========== STEPS FORM ========== -->
        <form id="procedure-steps-form" class="space-y-6">
            <hr class="border-gray-300">
            <p class="text-center font-bold text-gray-700 uppercase">Steps</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Responsibility & Activities -->
                <div>
                    <label for="responsibility" class="block text-xs font-bold uppercase mb-1">Responsibility</label>
                    <input type="text" id="responsibility" name="responsibility[]" required
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2 mb-3" />

                    <label for="activities" class="block text-xs font-bold uppercase mb-1">Activities</label>
                    <textarea id="activities" name="activities[]" rows="5" required
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"></textarea>
                </div>

                <!-- Notes -->
                <div>
                    <label for="note" class="block text-xs font-bold uppercase mb-1">Include a Note (optional)</label>
                    <textarea id="note" name="note[]" rows="8"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"></textarea>
                </div>

                <!-- Interfaces -->
                <div>
                    <label for="interfaces_input" class="block text-xs font-bold uppercase mb-1">Interfaces (References)</label>
                    <textarea id="interfaces_input" name="interfaces_inputs[]" rows="3"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2 mb-3"></textarea>

                    <label for="interfaces_output" class="block text-xs font-bold uppercase mb-1">Interfaces (Outputs)</label>
                    <textarea id="interfaces_output" name="interfaces_outputs[]" rows="3"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2"></textarea>
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
                        <th class="border px-3 py-2">Responsibility</th>
                        <th class="border px-3 py-2">Actions</th>
                        <th class="border px-3 py-2">Note</th>
                        <th class="border px-3 py-2">Interfaces (References)</th>
                        <th class="border px-3 py-2">Interfaces (Outputs)</th>
                    </tr>
                </thead>
                <tbody id="procedure-steps-table">
                    <tr id="noDataRow">
                        <td colspan="5" class="text-center py-3 text-gray-500">No Procedure Steps Added</td>
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

<script>
    // HANDLE EXPENSE DETAILS FUNCTIONALITY
    let steps = [];
    document.getElementById('procedure-steps-form').addEventListener('submit', function (e) {
        e.preventDefault();
        addStep();
    });

    function addStep() {
        const step = {
            responsibility: document.getElementById('responsibility').value,
            activities: document.getElementById('activities').value,
            note: document.getElementById('note').value,
            interfaces_input: document.getElementById('interfaces_input').value,
            interfaces_output: document.getElementById('interfaces_output').value
        };

        steps.push(step);
        updateProcedureTable();
        updateHiddenJsonField();
        clearStepsForm();
    }

    function updateProcedureTable() {
        const tbody = document.querySelector('#procedure-steps-table tbody');
        tbody.innerHTML = '';

        steps.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="border border-gray-300 px-3 py-2 align-top">${item.responsibility}</td>
                <td class="border border-gray-300 px-3 py-2 align-top">${item.activities}</td>
                <td class="border border-gray-300 px-3 py-2 align-top">${item.note}</td>
                <td class="border border-gray-300 px-3 py-2 align-top">${item.interfaces_input}</td>
                <td class="border border-gray-300 px-3 py-2 align-top">${item.interfaces_output}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function updateHiddenJsonField() {
        document.getElementById('procedure-steps-json').value = JSON.stringify(steps);
    }

    function clearStepsForm() {
        document.getElementById('procedure-steps-form').reset();
    }
</script>
</x-layout>