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
                <a href="{{ route('document.index') }}">Document Management</a> > Management System Manual
            </h1>
            <div>
            <a href="{{ route('document.ms_manual.create')}}"
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
                        <th class="px-4 py-3">Section #</th>
                        <th class="px-4 py-3">Section Title</th>
                        <th class="px-4 py-3">Pages</th>
                        <th class="px-4 py-3">Revision Number</th>
                        <th class="px-4 py-3">Effective Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ms_manuals as $manual)
                        <tr>
                            <td>{{$manual->section_number}}</td>
                            <td>{{$manual->title}}</td>
                            <td>{{$manual->pages}}</td>
                            <td>{{$manual->revision_number ?? 'N/A'}}</td>
                            <td>{{$manual->effective_date ?? 'N/A'}}</td>
                            <td>{{$manual->status}}</td>
                            <td class="py-2 text-center items-center space-x-2">
                                <a href="{{ route('document.ms_manual.view', $manual->id) }}" class="text-gray-600 hover:text-sky-700">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                language: {
                    lengthMenu: 'Show _MENU_ rows'
                },
                columnDefs: [
                    {targets: [6], orderable: false}
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
        </script>
    </x-slot:scripts>
</x-layout>