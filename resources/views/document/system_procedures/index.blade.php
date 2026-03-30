<x-layout>
    <style>
        .collapse-content {
            border-radius: 15px;
            padding: 10px 15px;
            background-color: #f8f8f8;
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #section_documents thead th {
            font-size: 10px;
            background: transparent;
        }

        #section_documents tbody tr td {
            border-bottom: 1px solid rgba(243, 244, 246, 1);
        }

        /* Status Pill Styling for Workflow */
        .status-pill-workflow {
            background: rgba(87, 93, 249, 0.1);
            color: #575df9;
            border: 1px solid rgba(87, 93, 249, 0.2);
        }
        
        .status{
            padding: 5px 10px;
            border-radius: 15px;
            width: 140px;
        }
        .Active{
            color: #f1f1f1;
            background-color: #50C878;
        }
        .Draft{
            color: #f1f1f1;
            background-color: #96DED1;
        }
        .For_Review{
            color: #f1f1f1;
            background-color: #E1C16E;
        }
        .For_Revision{
            color: #f1f1f1;
            background-color: gray;
        }
        .For_Approval{
            color: #f1f1f1;
            background-color: #DAA520;
        }
        .Review_not_Passed{
            color: #f1f1f1;
            background-color: #96DED1;
        }
        .Pending_Code{
            color: #f1f1f1;
            background-color: #50C878;
        }
        .Not_Approved{
            color: #f1f1f1;
            background-color: #96DED1;
        }
        input:read-only{
            background-color: lightgray;
        }
        .input-group{
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .input-container{
            display: flex;
            flex-direction: row;
            height: 2.5rem;
            width: 100%;
        }
        .input-label{
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 0.25rem;
            padding: 0.5rem;
            border-top-left-radius: 1rem;
            border-bottom-left-radius: 1rem;
            --tw-border-opacity: 1;
            border-color: rgba(156, 163, 175, var(--tw-border-opacity));
            border-top-width: 1px;
            border-left-width: 1px;
            border-bottom-width: 1px;
            width: 50%;
            text-align: left;
            color: gray;
        }
        .input-append{
            height: 2.5rem;
            width: 50%;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.75rem;
            border-width: 1px;
            --tw-border-opacity: 1;
            border-color: rgba(156, 163, 175, var(--tw-border-opacity)) !important;
            border-left-width: 0px !important;
            border-radius: 0px !important;
            border-top-right-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
            outline: 1px solid transparent;
            outline-offset: 1px;
        }
    </style>
    <div class="mx-auto w-full px-5 py-3">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">System Procedures</h3>
            <div>
                <a href="{{ route('document.system_procedures.create') }}" 
                class="inline-flex items-center gap-2 bg-[#575df9] text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow hover:bg-[#464bd4] hover:shadow-[#575df9]/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Create Document</span>
                </a>
            {{-- <a href="{{ route('document.system_procedures.dirf_generate', 1)}}"
            class="inline-block bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
                DIRFs
            </a> --}}
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden bg-white rounded-3xl border border-gray-100 shadow-sm px-5">
            <div class="overflow-x-auto">
                <table id="sections-table" class="w-full text-sm text-left border-separate border-spacing-0">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-5 border-b border-gray-100 text-center w-16">
                                <button id="toggleAll" type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-gray-200 bg-white text-gray-500 hover:text-[#575df9] hover:border-[#575df9] transition shadow-sm" title="Expand / Collapse all">
                                    <span id="toggleIcon" class="text-lg font-bold leading-none">+</span>
                                </button>
                            </th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">No.</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Section Title</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Process Owner</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest text-center">Stakeholders</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Docs</th>
                            <th class="px-4 py-5 border-b border-gray-100 w-16"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-50">
                        @foreach($sections as $section)
                        <tr class="group hover:bg-slate-50/50 transition-colors" data-id="{{$section->section_number}}">
                            <td class="details-control dt-control px-6 py-4 text-center"></td>
                            
                            <td class="px-4 py-4 font-bold text-gray-900">{{$section->section_number}}</td>
                            
                            <td class="px-4 py-4">
                                <span class="block font-bold text-gray-800 group-hover:text-[#575df9] transition-colors">
                                    {{$section->title}}
                                </span>
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-7 w-7 rounded-full bg-indigo-50 text-[#575df9] flex items-center justify-center text-[10px] font-bold border border-indigo-100">
                                        {{ substr($section->processOwner->first_name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="text-gray-600 font-medium">
                                        {{ $section->processOwner->last_name ?? '' }}, {{ $section->processOwner->first_name ?? '' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-gray-400 uppercase w-12">Reviewer:</span>
                                        <span class="text-xs text-gray-600">{{ $section->reviewer->last_name}}, {{ $section->reviewer->first_name}}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-gray-400 uppercase w-12">Approver:</span>
                                        <span class="text-xs text-gray-600">{{ $section->approver->last_name}}, {{ $section->approver->first_name}}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#3de3b1]/10 text-[#2db68e]">
                                    {{$section->count}}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-right">
                                <button type="button" class="h-8 w-8 rounded-lg text-gray-400 hover:text-[#575df9] hover:bg-white hover:shadow-sm transition"
                                onclick="openEditModal(
                                    {{ $section->id }},
                                    '{{ addslashes($section->section_number) }}',
                                    '{{ addslashes($section->title) }}',
                                    '{{ addslashes($section->processOwner->last_name ?? '') }}, {{ addslashes($section->processOwner->first_name ?? '') }}{{ $section->processOwner->middle_name ? ' ' . addslashes($section->processOwner->middle_name) : '' }}',
                                    '{{ $section->processOwner->id ?? '' }}',
                                    '{{ addslashes($section->reviewer->last_name ?? '') }}, {{ addslashes($section->reviewer->first_name ?? '') }}{{ $section->reviewer->middle_name ? ' ' . addslashes($section->reviewer->middle_name) : '' }}',
                                    '{{ $section->reviewer->id ?? '' }}',
                                    '{{ addslashes($section->approver->last_name ?? '') }}, {{ addslashes($section->approver->first_name ?? '') }}{{ $section->approver->middle_name ? ' ' . addslashes($section->approver->middle_name) : '' }}',
                                    '{{ $section->approver->id ?? '' }}'
                                )">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="bg-gray-50/30">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right font-bold text-gray-400 uppercase text-[10px] tracking-widest">Total Documents:</td>
                            <td class="px-4 py-4 font-black text-[#575df9] text-lg">{{$totalCount}}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="fixed inset-0 bg-gray-100/50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <div class="flex justify-between items-center border-b mb-2">
                <h2 class="text-lg font-semibold">Edit Section</h2>
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 text-2xl">
                    &times;
                </button>
            </div>
            <div id="editFormMessage" class="mb-4 hidden">
                <div id="editFormMessageText" class="text-sm rounded px-4 py-2"></div>
            </div>
            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4 flex gap-4">
                    <div class="w-1/4">
                        <label class="block text-sm font-medium mb-1">Section Number</label>
                        <input type="text" name="section_number" id="edit_section_number" class="w-full border border-gray-300 rounded px-3 py-2" readonly>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="title" id="edit_title" class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>

                <div class="mb-4 relative">
                    <label class="block text-sm font-medium mb-1">Process Owner</label>
                    <input type="text" autocomplete="off" id="edit_process_owner" class="w-full border border-gray-300 rounded px-3 py-2 autocomplete-input">
                    <input type="hidden" name="process_owner_id" id="edit_process_owner_id">
                    <div id="edit_process_owner_list" class="autocomplete-results absolute left-0 right-0 bg-white border border-gray-300 mt-1 max-h-40 overflow-auto hidden z-50"></div>
                </div>

                <div class="mb-4 relative">
                    <label class="block text-sm font-medium mb-1">Reviewer</label>
                    <input type="text" autocomplete="off" id="edit_reviewer" class="w-full border border-gray-300 rounded px-3 py-2 autocomplete-input">
                    <input type="hidden" name="reviewer_id" id="edit_reviewer_id">
                    <div id="edit_reviewer_list" class="autocomplete-results absolute left-0 right-0 bg-white border border-gray-300 mt-1 max-h-40 overflow-auto hidden z-50"></div>
                </div>

                <div class="mb-4 relative">
                    <label class="block text-sm font-medium mb-1">Approver</label>
                    <input type="text" autocomplete="off" id="edit_approver" class="w-full border border-gray-300 rounded px-3 py-2 autocomplete-input">
                    <input type="hidden" name="approver_id" id="edit_approver_id">
                    <div id="edit_approver_list" class="autocomplete-results absolute left-0 right-0 bg-white border border-gray-300 mt-1 max-h-40 overflow-auto hidden z-50"></div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 border rounded" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Set Code Modal --}}
    <div id="assignCodeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Assign Document Code</h2>
                <button type="button" onclick="closeAssignCodeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="assignCodeForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Document Title</label>
                        <input type="text" id="modal_document_title" class="mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2" readonly>
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <div class="w-full whitespace-nowrap"> Final Document Code <span class="text-red-500">*</span></div>
                            </div>
                            <input id="modal_document_code" type="text" name="document_code" class="input-append focus:ring-0 focus:border-blue-500 @error('document_code') is-invalid @enderror" required/>
                        </div>
                        @error('document_code')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <div class="w-full whitespace-nowrap"> Revision Number <span class="text-red-500">*</span></div>
                            </div>
                            <input id="modal_revision_number" type="text" name="revision_number" class="input-append focus:ring-0 focus:border-blue-500 @error('revision_number') is-invalid @enderror" required/>
                        </div>
                        @error('revision_number')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <span class="italic text-xs text-gray-400">
                    * The fields have been pre-filled with suggested code input by the process owner and revision number based from past version/s.
                </span>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeAssignCodeModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        Assign Code
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500/50 transition-opacity" aria-hidden="true" onclick="closeDeleteModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 15c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Archive Document: <span id="modalDocCode" class="text-red-600"></span>
                        </h3>
            
                        <p class="mt-2 text-sm text-gray-500">
                            Please provide a justification for archiving this document. This will be stored in the audit trail.
                        </p>
                        <div class="mt-4">
                            <form id="deleteForm" method="POST" action="">
                                @csrf
                                @method('DELETE')
                
                                <label class="block text-sm font-medium text-gray-700">Justification</label>
                                <textarea name="delete_justification" id="justificationInput" required rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" placeholder="e.g. This procedure will be moved/combined with another procedure"></textarea>
                                
                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 cursor-pointer">
                                        Cancel
                                    </button>
                                    <button type="submit" id="confirmDeleteBtn" disabled class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                        Confirm Archive
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            function openEditModal(id, sectionNumber, title, processOwnerName='', processOwnerId='', reviewerName='', reviewerId='', approverName='', approverId=''){
                document.getElementById('editSectionForm').action = `/document/system_procedures/${id}`;
                document.getElementById('edit_section_number').value = sectionNumber;
                document.getElementById('edit_title').value = title;

                // Populate names
                document.getElementById('edit_process_owner').value = processOwnerName;
                document.getElementById('edit_reviewer').value = reviewerName;
                document.getElementById('edit_approver').value = approverName;

                // Populate hidden IDs
                document.getElementById('edit_process_owner_id').value = processOwnerId;
                document.getElementById('edit_reviewer_id').value = reviewerId;
                document.getElementById('edit_approver_id').value = approverId;

                document.getElementById('editSectionForm').action =`/document/system-procedures/${id}`;

                document.getElementById('editModal').classList.remove('hidden');
            }

            function closeEditModal(){
                document.getElementById('editModal').classList.add('hidden');
            }

            function openAssignCodeModal(id, title, suggestedCode, documentCode, revisionNumber) {
                const modal = document.getElementById('assignCodeModal');
                const form = document.getElementById('assignCodeForm');

                document.getElementById('modal_document_title').value = title || '';
                document.getElementById('modal_document_code').value = documentCode || suggestedCode || '';
                document.getElementById('modal_revision_number').value = revisionNumber || '0';

                form.action = `/documents/system-procedures/${id}/assign-code`;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeAssignCodeModal() {
                const modal = document.getElementById('assignCodeModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }


            window.canViewRevisionHistory = @json(auth()->user()->role === 'Document Controller');
            window.canReview = @json(auth()->user()->role === 'Reviewer');
            window.canApprove = @json(auth()->user()->role === 'Approver');
            window.canSend = @json(auth()->user()->role === 'User');
            const loadedSections = {};

            let table = new DataTable('#sections-table', {
                order: [[1, 'asc']],
                "autoWidth": false,
                "responsive": true,
                columnDefs: [
                    {targets: [0, 6], orderable: false}
                ],
                "paging": false,
                "searching": false
            });

            $('#sections-table tbody').on('click', 'td.details-control', function () {
                const tr = $(this).closest('tr');
                const row = table.row(tr);
                const sectionId = tr.data('id');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    return;
                }
                tr.addClass('shown');

                // If already loaded, reuse it
                if (loadedSections[sectionId]) {
                    row.child(loadedSections[sectionId]).show();
                    return;
                }

                $.ajax({
                    url: `/section/documents/`,
                    type: 'GET',
                    data: { sectionId: sectionId }, // Pass sectionId as a data parameter
                    beforeSend: function () {
                        row.child('<div class="loader"></div>').show();
                    },
                    success: function (data) {
                        const details = formatDetails(data);
                        loadedSections[sectionId] = details; // cache
                        row.child(details).show();
                    },
                    error: function () {
                        row.child('<div>Failed to load details. Please try again.</div>').show();
                    },
                });
            });

            function formatDetails(data) {
                let itemsTable = `
                    <div class="p-4 rounded-b-2xl border-x border-b border-gray-100">
                        <table class="w-full text-xs text-left" id="section_documents">
                            <thead>
                                <tr class="text-gray-400 uppercase tracking-widest border-b border-gray-200">
                                    <th class="px-4 py-3 w-2/6">Document Title</th>
                                    <th class="px-4 py-3 text-center!">Code</th>
                                    <th class="px-4 py-3 text-center!">Status</th>
                                    <th class="px-4 py-3 text-center">Rev</th>
                                    <th class="px-4 py-3 text-center">Effective Date</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">`;

                let index = 1;
                if (!data.items || data.items.length === 0) {
                    itemsTable += `<tr><td colspan="6" class="text-center py-8 text-gray-400 italic font-medium">No documents currently in this section.</td></tr>`;
                } else {
                    data.items.forEach((details) => {
                        let statusColor = details.status === 'Active' ? 'bg-[#3de3b1]/10 text-[#2db68e]' : 'bg-gray-100 text-gray-500';
                        if (details.status.includes('Review') || details.status.includes('Approval')) {
                            statusColor = 'bg-[#575df9]/10 text-[#575df9]'; // Use Brand Blue for Workflow
                        }
                        let pageInfo = `<i class="fa-solid fa-circle-info mr-1"></i>View Document First`;
                        itemsTable += `
                            <tr class="hover:bg-white transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800 uppercase tracking-tight">${details.title}</span>
                                        <span class="text-[9pt] text-gray-400 font-medium">${details.pages>1 ? details.pages + ' pages' : details.pages == 1 ? details.pages + ' page' : pageInfo}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono text-gray-500">${details.code}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[9pt] uppercase ${statusColor}">
                                        ${details.status}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center text-gray-600 font-bold">
                                    ${details.status === 'Active' ? details.revision_number : '—'}
                                </td>
                                <td class="px-4 py-4 text-center text-gray-500">
                                    ${formatDate(details.effective_date)}
                                </td>
                                <td class="py-2 items-center space-x-2 w-38">
                                    <a href="${details.viewUrl}" class="inline-flex items-center justify-center text-gray-600 hover:text-sky-700">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>`;
                        if(details.status !== 'For Review' && details.status !== 'For Approval' && details.can.edit){
                            itemsTable += `
                                <a href="${details.editUrl}" class="inline-flex items-center justify-center text-gray-600 hover:text-sky-700">
                                    <i class="fa-solid fa-pen"></i>
                                </a>`;
                        }
                        if(details.can.delete){
                            itemsTable += `
                                    <button type="button" onclick="openDeleteModal('${details.id}', '${details.code}')" class="inline-flex text-red-600 hover:text-red-900 font-medium text-sm flex items-center justify-center transition-colors" title="Archive Document">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>`;
                        }
                        if((details.status === 'Draft' || details.status === 'For Revision') && details.can.edit){
                            itemsTable += `
                            <span class="text-gray-400">|</span>
                            <form action="${details.sendForReviewUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to send this document for review? You will not be able to make changes.\');" class="inline">
                                <input type="hidden" name="_token" value="${data.csrf}">
                                <input type="hidden" name="_method" value="PUT">

                                <button type="submit" class="text-gray-600 hover:text-sky-700 cursor-pointer" title="Send For Review">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                            `;
                        }else if(details.can.review){
                            itemsTable += `
                            <span class="text-gray-400">|</span>
                            <div class="inline-flex rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <form action="${details.reviewDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to send this document for approval? You will not be able to make changes.\');" class="inline">
                                    <input type="hidden" name="_token" value="${data.csrf}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="decision" value="pass">

                                    <button type="submit" class="px-2 py-1 bg-white hover:bg-green-50 text-green-600 border-r border-gray-200 cursor-pointer" title="Pass and Send For Approval">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <form action="${details.reviewDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to fail document review and send back? You will not be able to make changes.\');" class="inline">
                                    <input type="hidden" name="_token" value="${data.csrf}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="decision" value="fail">

                                    <button type="submit" class="px-2 py-1 bg-white hover:bg-red-50 text-red-600 cursor-pointer" title="Fail Review">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                            `;
                        }else if(details.can.approve){
                            itemsTable += `
                            <span class="text-gray-400">|</span>
                            <div class="inline-flex rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <form action="${details.approveDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to approve this document? You will not be able to make changes and this will mark the document as Active.\');" class="inline">
                                    <input type="hidden" name="_token" value="${data.csrf}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="decision" value="pass">

                                    <button type="submit" class="px-2 py-1 bg-white hover:bg-green-50 text-green-600 border-r border-gray-200 cursor-pointer" title="Document Approved and Make Active">
                                        <i class="fa-solid fa-check-double"></i>
                                    </button>
                                </form>
                                <form action="${details.approveDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to not approve this document? You will not be able to make changes.\');" class="inline">
                                    <input type="hidden" name="_token" value="${data.csrf}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="decision" value="fail">

                                    <button type="submit" class="px-2 py-1 bg-white hover:bg-red-50 text-red-600 cursor-pointer" title="Document Not Approved">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                            `;
                        }else if(details.can.setCode){
                            itemsTable += `
                                <span class="text-gray-400">|</span>
                                <button type="button" class="text-gray-600 hover:text-blue-700" onclick="openAssignCodeModal('${details.id}', '${details.title}', '${details.code}', '${details.code}', '${details.revision_number}')" title="Assign Code">
                                    <i class="fa-solid fa-file-pen"></i>
                                </button>
                            `;
                        }

                        if(details.can.review || details.can.approve){
                            itemsTable += `<a href="/documents/system-procedures/${details.id}/comment" class="text-gray-600 hover:text-blue-700 cursor-pointer" title="Leave Comments and Send Back">
                                <i class="fa-solid fa-comment"></i>
                            </a>`;
                        }
                        itemsTable += `
                            </td>
                        </tr>`;
                    });
                }

                itemsTable += `
                </tbody></table>`;
                
                return `<div class="collapse-content">${itemsTable}</div>`;
            }

            function formatDate(dateString) {
                if (!dateString) return 'N/A';
                
                const date = new Date(dateString);
                const day = date.getDate();
                const month = date.toLocaleString('en-US', { month: 'short' });
                const year = date.getFullYear();

                return `${day} ${month} ${year}`; // Format: "16 Jul 2024"
            }
            let allExpanded = false;

            $('#toggleAll').on('click', function () {
                expandAll();
            });

            function expandAll(){
                allExpanded = !allExpanded;

                table.rows({ page: 'current' }).every(function () {
                    const tr = $(this.node());
                    const row = this;
                    const sectionId = tr.data('id');

                    if (allExpanded && !row.child.isShown()) {
                        tr.addClass('shown');// If already loaded, reuse it
                        if (loadedSections[sectionId]) {
                            row.child(loadedSections[sectionId]).show();
                            return;
                        }

                        $.ajax({
                            url: `/section/documents/`,
                            type: 'GET',
                            data: { sectionId: sectionId }, // Pass sectionId as a data parameter
                            beforeSend: function () {
                                row.child('<div class="loader"></div>').show();
                            },
                            success: function (data) {
                                const details = formatDetails(data);
                                loadedSections[sectionId] = details; // cache
                                row.child(details).show();
                            },
                            error: function () {
                                row.child('<div>Failed to load details. Please try again.</div>').show();
                            },
                        });
                    }else if(!allExpanded) {
                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                        }
                    }
                });

                $('#toggleIcon').text(allExpanded ? '−' : '+');
            }
            document.addEventListener('DOMContentLoaded', (event) => {
                expandAll();
            });

            // --- AUTOCOMPLETE FUNCTION ---
            function setupAutocomplete(inputId, hiddenId, listId, fetchUrl){
                const input = document.getElementById(inputId);
                const hiddenInput = document.getElementById(hiddenId);
                const list = document.getElementById(listId);
                let currentFocus = -1;

                input.addEventListener('input', function(){
                    const query = this.value;
                    hiddenInput.value = '';
                    currentFocus = -1;
                    if(!query){
                        list.innerHTML = '';
                        list.classList.add('hidden');
                        return;
                    }

                    fetch(`${fetchUrl}?query=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(users => {
                            if(!users.length){
                                list.innerHTML = '<div class="px-3 py-2 text-gray-500">No results</div>';
                            } else {
                                list.innerHTML = users.map(user => {
                                    const fullName = `${user.last_name}, ${user.first_name}${user.middle_name ? ' ' + user.middle_name : ''}`;
                                    return `<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" onclick="selectUser('${inputId}', '${hiddenId}', ${user.id}, '${fullName}')">${fullName}</div>`;
                                }).join('');
                            }
                            list.classList.remove('hidden');
                        });
                });

                // Keyboard navigation
                input.addEventListener('keydown', function(e){
                    let x = list.getElementsByTagName('div');
                    if(!x) return;

                    if(e.keyCode == 40){ // Arrow DOWN
                        currentFocus++;
                        addActive(x);
                    } else if(e.keyCode == 38){ // Arrow UP
                        currentFocus--;
                        addActive(x);
                    } else if(e.keyCode == 13){ // ENTER
                        e.preventDefault();
                        if(currentFocus > -1){
                            if(x) x[currentFocus].click();
                        }
                    }
                });

                function addActive(x){
                    if(!x) return;
                    removeActive(x);
                    if(currentFocus >= x.length) currentFocus = 0;
                    if(currentFocus < 0) currentFocus = x.length - 1;
                    x[currentFocus].classList.add('bg-gray-200');
                }

                function removeActive(x){
                    for(let i = 0; i < x.length; i++){
                        x[i].classList.remove('bg-gray-200');
                    }
                }

                document.addEventListener('click', function(e){
                    if(!input.contains(e.target) && !list.contains(e.target)){
                        list.classList.add('hidden');
                    }
                });
            }

            function selectUser(inputId, hiddenId, userId, fullName){
                document.getElementById(inputId).value = fullName;
                document.getElementById(hiddenId).value = userId;
                document.getElementById(inputId + '_list').classList.add('hidden');
            }

            // Setup all three autocomplete fields
            setupAutocomplete('edit_process_owner','edit_process_owner_id','edit_process_owner_list','/users/search');
            setupAutocomplete('edit_reviewer','edit_reviewer_id','edit_reviewer_list','/users/search');
            setupAutocomplete('edit_approver','edit_approver_id','edit_approver_list','/users/search');

            
            document.getElementById('editSectionForm').addEventListener('submit', function(e){
                e.preventDefault();

                const form = this;
                const url = form.action;
                const messageBox = document.getElementById('editFormMessage');
                const messageText = document.getElementById('editFormMessageText');

                messageBox.classList.add('hidden');
                messageText.innerHTML = '';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                .then(async response => {
                    const data = await response.json();

                    if(!response.ok){
                        throw data;
                    }

                    messageText.className = 'text-sm rounded px-4 py-2 bg-green-100 text-green-800';
                    messageText.textContent = data.message;
                    messageBox.classList.remove('hidden');

                    setTimeout(() => location.reload(), 800);
                })
                .catch(error => {
                    messageText.className = 'text-sm rounded px-4 py-2 bg-red-100 text-red-800';

                    if(error.errors){
                        messageText.innerHTML = Object.values(error.errors)
                            .map(err => `<div>${err[0]}</div>`)
                            .join('');
                    } else {
                        messageText.textContent = 'Something went wrong. Please try again.';
                    }

                    messageBox.classList.remove('hidden');
                });
            });

            /**
             * Opens the Delete Modal and sets the target document context
             * @param {string|number} docId - The primary key of the document
             * @param {string} docCode - The readable code (e.g., SP-BPL-01)
             */
            function openDeleteModal(docId, docCode) {
                const modal = document.getElementById('deleteModal');
                const form = document.getElementById('deleteForm');
                const codeSpan = document.getElementById('modalDocCode');
                const input = document.getElementById('justificationInput');
                const btn = document.getElementById('confirmDeleteBtn');
                
                form.action = `/documents/${docId}`;
                
                codeSpan.innerText = docCode;
                input.value = '';
                btn.disabled = true;
                
                modal.classList.remove('hidden');
                
                setTimeout(() => input.focus(), 100);
                
            document.getElementById('justificationInput').addEventListener('input', function(e) {
                const btn = document.getElementById('confirmDeleteBtn');
                btn.disabled = e.target.value.trim().length < 0;
            });
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }
        </script>
    </x-slot:scripts>
</x-layout>