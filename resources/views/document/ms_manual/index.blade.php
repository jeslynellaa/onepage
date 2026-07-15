<x-layout>
    <style>
        .collapse-content {
            border-radius: 15px;
            padding: 10px 15px;
            background-color: #f1f1f1;
        }
        
        .status{
            padding: 5px 10px;
            border-radius: 15px;
            width: 140px;
        }
        .Active{
            color: #2db68e;
            background-color: #3de3b150;
        }
        .Draft{
            color: #6b7280;
            background-color: #f3f4f6;
        }
        .For_Review{
            color: #575df9;
            background-color: #575df910;
        }
        .For_Revision{
            color: #6b7280;
            background-color: #f3f4f6;
        }
        .For_Approval{
            color: #575df9;
            background-color: #575df910;
        }
        .Review_not_Passed{
            color: #6b7280;
            background-color: #f3f4f6;
        }
        .Pending_Code{
            color: #6b7280;
            background-color: #f3f4f6;
        }
        .Not_Approved{
            color: #6b7280;
            background-color: #f3f4f6;
        }
        input:read-only{
            background-color: lightgray;
        }
    </style>
    <div class="mx-auto w-full px-5 py-3">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Management System Manual</h3>
            <div>
                <a href="{{ route('document.ms_manual.create')}}"
                class="inline-flex items-center gap-2 bg-[#575df9] text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow hover:bg-[#464bd4] hover:shadow-[#575df9]/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Create Document</span>
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden bg-white rounded-3xl shadow-sm px-5">
            <div class="overflow-x-auto">
                <table id="sections-table" class="w-full text-sm text-left border-separate border-spacing-0">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">No.</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Section Title</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Status</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Rev</th>
                            <th class="px-4 py-5 border-b border-gray-100 font-bold text-gray-400 uppercase text-[10px] tracking-widest">Effective Date</th>
                            <th class="px-4 py-5 border-b border-gray-100 w-16"></th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($docs as $manual)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-900">{{$manual['section_number']}}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="block font-bold text-gray-800 group-hover:text-[#575df9] transition-colors">
                                            {{$manual["title"]}}
                                        </span>
                                        <span class="text-[9pt] text-gray-400 font-medium">{{$manual['pages']>1 ? $manual["pages"]." pages" : $manual['pages']." page"}}</span>
                                    </div>
                                </td>
                                <td class="py-2 text-center items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[9pt] uppercase {{$manual['status']}}">{{$manual['status']}}</span>
                                </td>
                                <td class="px-4 py-4 text-center text-gray-600 font-bold">{{$manual['revision_number'] ?? '—'}}</td>
                                <td class="px-4 py-4 text-center text-gray-500">{{$manual['effective_date'] ? $manual['effective_date']->format('M d Y') : 'N/A'}}</td>
                                <td class="py-2 items-center space-x-2 w-38">
                                    <a href="{{ route('document.ms_manual.view', $manual['id']) }}" class="inline-flex items-center justify-center text-gray-600 hover:text-sky-700" title="View Document">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if ($manual['status'] !== 'For Review' && $manual['status'] !== 'For Approval' && $manual['can']['edit'])
                                        <a href="{{$manual['editUrl']}}" class="inline-flex items-center justify-center text-gray-600 hover:text-sky-700" title="Edit Document">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endif
                                    
                                    @if ($manual['can']['delete'])
                                        <button type="button" onclick="openDeleteModal({{$manual['id']}}, {{$manual['id']}})" class="inline-flex text-red-600 hover:text-red-900 font-medium text-sm flex items-center justify-center transition-colors" title="Archive Document">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif

                                    @if (($manual['status'] !== 'Draft' || $manual['status'] !== 'For Revision') && $manual['can']['edit'])
                                        <span class="text-gray-400">|</span>
                                        <form action="{{$manual['sendForReviewUrl']}}" method="POST" onsubmit="return confirm(\'Are you sure you want to send this document for review? You will not be able to make changes.\');" class="inline">
                                            <input type="hidden" name="_token" value="${data.csrf}">
                                            <input type="hidden" name="_method" value="PUT">

                                            <button type="submit" class="text-gray-600 hover:text-sky-700 cursor-pointer" title="Send For Review">
                                                <i class="fa-solid fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if ($manual['can']['review'])
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
                                    @endif
                                    
                                    @if ($manual['can']['approve'])
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
                                    @endif
                                    
                                    @if ($manual['can']['review'] || $manual['can']['approve'])
                                        <a href="" class="text-gray-600 hover:text-blue-700 cursor-pointer" title="Leave Comments and Send Back">
                                            <i class="fa-solid fa-comment"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
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


            window.canViewRevisionHistory = @json(auth()->user()->role === 'Document Controller');
            window.canReview = @json(auth()->user()->role === 'Reviewer');
            window.canApprove = @json(auth()->user()->role === 'Approver');
            window.canSend = @json(auth()->user()->role === 'User');
            const loadedSections = {};

            let table = new DataTable('#sections-table', {
                order: [[0, 'asc']],
                "autoWidth": false,
                "responsive": true,
                language: {
                    lengthMenu: 'Show _MENU_ rows'
                },
                columnDefs: [
                    {targets: [5], orderable: false}
                ],
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
                const input = document.getElementById('justificationInput');
                const btn = document.getElementById('confirmDeleteBtn');
                
                form.action = `/documents/ms-manual/${docId}/destroy`;
                
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