<x-layout>
    <style>
        .collapse-content {
            border-radius: 15px;
            padding: 10px 15px;
            background-color: #f1f1f1;
        }
        .status{
            padding: 5px 18px;
            border-radius: 15px;
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
        .For_Approval{
            color: #f1f1f1;
            background-color: #DAA520;
        }
        .Review_not_Passed{
            color: #f1f1f1;
            background-color: #96DED1;
        }
        .Not_Approved{
            color: #f1f1f1;
            background-color: #96DED1;
        }
        input:read-only{
            background-color: lightgray;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > Support Documents
            </h1>
            <div>
            <a href="{{ route('document.support_document.create')}}"
            class="inline-block bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
                Create
            </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-2xl shadow-lg px-5 py-2">
            <table id="sections-table" class="w-full border border-gray-200 text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-2 py-3 text-center">
                            <div class="flex justify-center">
                                <button id="toggleAll" type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md
                                        border border-gray-300 hover:bg-gray-100 transition" title="Expand / Collapse all" >
                                    <span id="toggleIcon" class="text-lg leading-none">+</span>
                                </button>
                            </div>
                        </th>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Section Title</th>
                        <th class="px-4 py-3">Process Owner</th>
                        <th class="px-4 py-3">Reviewer</th>
                        <th class="px-4 py-3">Approver</th>
                        <th class="px-4 py-3">Count</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $section)
                    <tr class="align-middle" data-id="{{$section->section_number}}">
                        <td class="details-control dt-control"></td> <!-- Expand button -->
                        <td class="px-4 py-3">{{$section->section_number}}</td>
                        <td class="px-4 py-3">{{$section->title}}</td>
                        <td class="px-4 py-3">{{$section->processOwner->last_name ?? ''}}, {{$section->processOwner->first_name ?? ''}}{{$section->processOwner->middle_name ? ' ' . $section->processOwner->middle_name : ''}}</td>

                        <td class="px-4 py-3">{{$section->reviewer->last_name ?? ''}}, {{$section->reviewer->first_name ?? ''}} {{$section->reviewer->middle_name ?? ''}}</td>
                        <td class="px-4 py-3">{{$section->approver->last_name ?? ''}}, {{$section->approver->first_name ?? ''}} {{$section->approver->middle_name ?? ''}}</td>
                        <td class="px-4 py-3">{{$section->count}}</td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="text-gray-400 text-sm hover:text-sky-700" 
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
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$totalCount}}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
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
                    {targets: [0, 7], orderable: false}
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
                    url: `/section/sp/documents/`,
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
                    <table class="w-full text-sm text-gray-700" style="font-size: 14px;" id="section_documents">
                        <thead>
                            <tr>
                                <th class="px-4 border-b w-2/6">Title</th>
                                <th class="px-4 border-b !text-center">Code</th>
                                <th class="px-4 border-b !text-center">Pages</th>
                                <th class="px-4 border-b !text-center">Status</th>
                                <th class="px-4 border-b !text-center">Rev. No</th>
                                <th class="px-4 border-b !text-center">Effective Date</th>
                                <th class="px-4 border-b !text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;

                let index = 1;
                if (!data.items || data.items.length === 0) {
                    itemsTable += `
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500 italic">
                                No documents found for this section
                            </td>
                        </tr>
                    `;
                } else {
                    $.each(data.items, function (key, details) {
                        let statusStyle = details.status.replace(/\s/g, "_");

                        itemsTable += `
                            <tr class="align-middle">
                                <td class="py-2 w-2/6 uppercase">${details.title}</td>
                                <td class="py-2 text-center">${details.code}</td>
                                <td class="py-2 text-center">${details.pages ?? 'View Document First'}</td>
                                <td class="py-2 text-center"><span class="status whitespace-nowrap ${statusStyle}">${details.status}</span></td>
                                <td class="py-2 text-center">
                                    ${details.revision_number ?? "N/A"}
                                    ${
                                        details.can.viewRevisionHistory
                                            ? `<a href="${details.revHistoryUrl}"
                                                class="ml-1 text-gray-600 hover:text-sky-700"
                                                title="View Revision History">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                            </a>`
                                            : ''
                                    }
                                </td>
                                <td class="py-2 text-center">${formatDate(details.effective_date)}</td>
                                <td class="py-2 text-center items-center space-x-2">
                                    <a href="${details.viewUrl}" class="text-gray-600 hover:text-sky-700">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>`;
                        if(details.status !== 'For Review' && details.status !== 'For Approval' && details.can.edit){
                            itemsTable += `
                                <a href="${details.editUrl}" class="text-gray-600 hover:text-sky-700">
                                    <i class="fa-solid fa-pen"></i>
                                </a>`;
                        }
                        if(details.can.delete){
                            itemsTable += `
                                    <form action="${details.deleteUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this document?\');" style="display:inline;">
                                        <input type="hidden" name="_token" value="${data.csrf}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="text-gray-600 hover:text-sky-700 cursor-pointer transition-colors duration-300">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>`;
                        }
                        if(details.status === 'Draft' && details.can.edit){
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
                            <form action="${details.reviewDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to send this document for approval? You will not be able to make changes.\');" class="inline">
                                <input type="hidden" name="_token" value="${data.csrf}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="decision" value="pass">

                                <button type="submit" class="text-gray-600 hover:text-green-700 cursor-pointer" title="Pass and Send For Approval">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <form action="${details.reviewDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to fail document review and send back? You will not be able to make changes.\');" class="inline">
                                <input type="hidden" name="_token" value="${data.csrf}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="decision" value="fail">

                                <button type="submit" class="text-gray-600 hover:text-red-700 cursor-pointer" title="Fail Review">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                            <a href="/documents/system-procedures/${details.id}/comment" class="text-gray-600 hover:text-blue-700 cursor-pointer" title="Leave Comments and Send Back">
                                <i class="fa-solid fa-comment"></i>
                            </a>
                            `;
                        }else if(details.can.approve){
                            itemsTable += `
                            <span class="text-gray-400">|</span>
                            <form action="${details.approveDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to approve this document? You will not be able to make changes and this will mark the document as Active.\');" class="inline">
                                <input type="hidden" name="_token" value="${data.csrf}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="decision" value="pass">

                                <button type="submit" class="text-gray-600 hover:text-green-700 cursor-pointer" title="Document Approved and Make Active">
                                    <i class="fa-solid fa-check-double"></i>
                                </button>
                            </form>
                            <form action="${details.approveDecisionUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to not approve this document? You will not be able to make changes.\');" class="inline">
                                <input type="hidden" name="_token" value="${data.csrf}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="decision" value="fail">

                                <button type="submit" class="text-gray-600 hover:text-red-700 cursor-pointer" title="Document Not Approved">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                            `;
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
                            url: `/section/sp/documents/`,
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
        </script>
    </x-slot:scripts>
</x-layout>