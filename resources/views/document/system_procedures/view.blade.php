<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > <a href="{{ route('document.system_procedures')}}">System Procedures</a> > {{$doc->title}}
        </h1>
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="w-full lg:w-2/3">
                @if($userDistribution && (!$userDistribution->received_at || !$userDistribution->oriented_and_retrieved_at))
                    <div class="rounded-xl border p-4 shadow-sm bg-amber-50 border-amber-200">
                        <h3 class="font-bold text-amber-900 flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> 
                            Your Signature Required on This Revision
                        </h3>
                        
                        @if(!$userDistribution->received_at)
                            <form action="{{ route('document.system_procedures.acknowledge_receipt', $doc->id) }}" method="POST" class="flex justify-between items-center gap-4">
                                @csrf
                                <p class="text-sm text-amber-800">Please confirm you have received and read this newly updated document configuration.</p>
                                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs py-2 px-4 rounded-lg shadow whitespace-nowrap">
                                    Confirm Receipt
                                </button>
                            </form>
                        @elseif(!$userDistribution->oriented_and_retrieved_at)
                            <form action="{{ route('document.system_procedures.acknowledge_orientation', $doc->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="text-sm text-amber-800 flex items-start gap-2">
                                    <input type="checkbox" name="purge_confirmation" id="purge" required class="mt-1 rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                    <label for="purge" class="cursor-pointer select-none">
                                        I certify that I have been oriented regarding these structural adjustments and that all copies of past superseded revisions have been purged/destroyed.
                                    </label>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs py-2 px-4 rounded-lg shadow">
                                        Submit Sign-off Verification
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif
                <div class="w-full h-[600px] overflow-y-auto overflow-x-hidden -webkit-overflow-scrolling-touch">
                    <iframe src="{{ route('document.system_procedures.sp_preview', $doc->id) }}" class="w-full h-full border rounded-xl"></iframe><div class="lg:hidden">
                    <div class="bg-white border-2 border-dashed border-blue-200 rounded-[2rem] p-8 text-center shadow-md">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex justify-center items-center mx-auto mb-4">
                            <i class="fa-solid fa-file-pdf text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Company Profile</h3>
                        <p class="text-gray-500 mb-6 mt-2">Tap below to view our full documentation in high quality.</p>
                        
                        <a href="{{ asset('docs/your-file.pdf') }}" 
                        target="_blank" 
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full transition-transform active:scale-95 shadow-lg">
                            <i class="fa-solid fa-eye mr-2"></i> View Document
                        </a>
                        
                        <p class="text-xs text-gray-400 mt-4 italic">Best viewed on mobile via native PDF reader</p>
                    </div>
                </div>
                </div>
                <div>
                    <div>Justification</div>
                    <div class="rounded-xl p-2 border">{{$doc->justification}}</div>
                </div>
                <div class="border rounded-xl p-4 bg-white shadow-sm mt-2">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="font-bold text-gray-800 text-base">DIRF Distribution Tracking</h2>
                            <p class="text-xs text-gray-500">Real-time sign-off sheet for this current documented information revision lifecycle.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-gray-100 text-gray-700 font-medium text-xs px-2.5 py-1 rounded-full">
                                Total Recipients: {{ $doc->distributions->count() }}
                            </span>
                            
                            {{-- Controller Access Restriction Guardrail --}}
                            @if(auth()->user()->role === 'Document Controller' || auth()->user()->role === 'auditor')
                                <button onclick="openDistributionModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-1.5 px-3 rounded-lg flex items-center gap-1 transition shadow-sm">
                                    <i class="fa-solid fa-user-plus text-[10px]"></i> Add Recipients
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($doc->distributions->isEmpty())
                        <div class="text-sm text-gray-400 italic text-center py-4 border border-dashed rounded-lg">
                            No distribution targets assigned to this document version.
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 border text-sm">
                                <thead class="bg-gray-50">
                                    <tr class="text-gray-700 font-medium">
                                        <th class="px-4 py-3 text-left">Name</th>
                                        <th class="px-4 py-3 text-center">New Document Received? (Signature)</th>
                                        <th class="px-4 py-3 text-center">Superseded Info Retrieved & Oriented?</th>
                                        <th class="px-4 py-3 text-center">Management Table Updated?</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100 text-gray-600">
                                    @foreach($doc->distributions as $dist)
                                        <tr class="@if($dist->user_id === auth()->id()) bg-blue-50/50 @endif">
                                            {{-- Name Column --}}
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $dist->user->fullname() }}
                                                @if($dist->user_id === auth()->id())
                                                    <span class="ml-1 text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">You</span>
                                                @endif
                                            </td>
                                            
                                            {{-- Phase 1 Status --}}
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if($dist->received_at)
                                                    <span class="text-green-600 font-semibold flex items-center justify-center gap-1">
                                                        <i class="fa-solid fa-circle-check text-xs"></i> 
                                                        {{ $dist->received_at->format('m/d/Y h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs italic">Pending Confirmation</span>
                                                @endif
                                            </td>

                                            {{-- Phase 2 Status --}}
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if($dist->oriented_and_retrieved_at)
                                                    <span class="text-green-600 font-semibold flex items-center justify-center gap-1">
                                                        <i class="fa-solid fa-circle-check text-xs"></i> 
                                                        {{ $dist->oriented_and_retrieved_at->format('m/d/Y h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs italic">Pending Sign-off</span>
                                                @endif
                                            </td>

                                            {{-- Phase 3 Status (Document Controller Final Step) --}}
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if($dist->management_table_updated_at)
                                                    <span class="text-green-600 font-semibold flex items-center justify-center gap-1">
                                                        <i class="fa-solid fa-shield-check text-xs"></i> 
                                                        {{ $dist->management_table_updated_at->format('m/d/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs italic">Awaiting Controller</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                        </div>
                    @endif
                </div>
                @if(auth()->user()->role === 'Document Controller')
                    <div id="distributionModal" onclick="handleOutsideModalClick(event)" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4 backdrop-blur-xs">
                        <!-- Inner Modal Content (Added stopPropagation to ensure clicking inside doesn't close it) -->
                        <div onclick="event.stopPropagation()" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl transform transition-all flex flex-col max-h-[90vh]">
                            
                            <!-- Modal Header -->
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Add Personnel to Distribution</h3>
                                    <p class="text-xs text-gray-500">Search and compile a list of users to append to this document.</p>
                                </div>
                                <button onclick="closeDistributionModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <!-- Form -->
                            <form method="POST" action="{{ route('document.distribution.sync', $doc->id) }}" id="distributionForm" class="flex flex-col flex-1 overflow-hidden">
                                @csrf
                                
                                <!-- Autocomplete Search Area -->
                                <div class="relative mb-4">
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Search Employee Name</label>
                                    <div class="relative">
                                        <input type="text" id="userSearchInput" autocomplete="off" placeholder="Type a name (e.g., Juan)..."
                                            class="w-full rounded-xl border-gray-300 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                        </div>
                                    </div>

                                    <!-- Suggestion Dropdown Panel Box -->
                                    <div id="suggestionsBox" class="absolute left-0 right-0 mt-1 max-h-[200px] overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg z-50 hidden divide-y divide-gray-50 text-sm">
                                        <!-- Dynamic list injected by JS -->
                                    </div>
                                </div>

                                <!-- Selected Items List Panel -->
                                <div class="flex-1 overflow-y-auto mb-4">
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Recipients to be Added</label>
                                    <div id="selectedUsersContainer" class="space-y-2 p-2 border border-dashed border-gray-200 rounded-xl min-h-[100px] bg-gray-50 max-h-[240px] overflow-y-auto">
                                        <!-- Instantiated template nodes go here -->
                                        <div id="emptyStatePlaceholder" class="text-xs text-gray-400 italic text-center py-8">
                                            No employees staged yet. Type a name above to begin building your update list.
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Footers -->
                                <div class="mt-auto flex justify-end gap-2 border-t pt-3">
                                    <button type="button" onclick="closeDistributionModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-xl transition">
                                        Cancel
                                    </button>
                                    <button type="submit" id="saveDistributionBtn" disabled class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        Save Distribution List
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Live Search & DOM Scripting Logic -->
                    <script>
                        // Convert the PHP Collection cleanly to a JS Array safe for autocomplete filtration
                        const availableUsers = @json($availableUsers);
                        let selectedUsers = [];
                        let currentFocusIndex = -1;

                        const searchInput = document.getElementById('userSearchInput');
                        const suggestionsBox = document.getElementById('suggestionsBox');
                        const selectedContainer = document.getElementById('selectedUsersContainer');
                        const placeholder = document.getElementById('emptyStatePlaceholder');
                        const saveBtn = document.getElementById('saveDistributionBtn');

                        // Capture input typing changes
                        searchInput.addEventListener('input', function() {
                            renderSuggestions(this.value.trim());
                        });

                        // Keydown processing (Arrow Keys & Enter)
                        searchInput.addEventListener('keydown', function(e) {
                            const items = suggestionsBox.getElementsByClassName('suggestion-item');
                            
                            if (items.length === 0) return;

                            if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                currentFocusIndex++;
                                setActiveSuggestion(items);
                            } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                currentFocusIndex--;
                                setActiveSuggestion(items);
                            } else if (e.key === 'Enter') {
                                e.preventDefault(); // Stop standard form submissions when choosing users
                                if (currentFocusIndex > -1 && items[currentFocusIndex]) {
                                    items[currentFocusIndex].click();
                                }
                            }
                        });

                        // Generate and filter display items list dynamically
                        function renderSuggestions(val) {
                            const query = val.toLowerCase();
                            suggestionsBox.innerHTML = '';
                            currentFocusIndex = -1; // Reset selection index position indicator

                            if (!query) {
                                suggestionsBox.classList.add('hidden');
                                return;
                            }

                            const matches = availableUsers.filter(user => 
                                user.first_name.toLowerCase().includes(query) && 
                                !selectedUsers.some(selected => selected.id === user.id)
                            );

                            if (matches.length === 0) {
                                suggestionsBox.innerHTML = `<div class="p-3 text-xs text-gray-400 italic">No matching records found</div>`;
                                suggestionsBox.classList.remove('hidden');
                                return;
                            }

                            matches.forEach((user, index) => {
                                const row = document.createElement('div');
                                row.className = 'suggestion-item p-2.5 hover:bg-blue-50 cursor-pointer transition text-gray-700 flex flex-col';
                                row.setAttribute('data-index', index);
                                row.innerHTML = `
                                    <span class="font-medium text-sm text-gray-800">${user.first_name}</span>
                                    <span class="text-[10px] text-gray-400 font-mono uppercase">${user.department || 'General Staff'}</span>
                                `;
                                
                                // Clicking hooks the item
                                row.addEventListener('click', () => selectUser(user));
                                suggestionsBox.appendChild(row);
                            });

                            suggestionsBox.classList.remove('hidden');
                        }// Manage highlight styling for keyboard navigation selectors
        function setActiveSuggestion(items) {
            if (!items) return;
            
            // Wipe existing highlighted styling blocks
            Array.from(items).forEach(item => item.classList.remove('bg-blue-100', 'hover:bg-blue-50'));

            // Boundary wrapping rules logic
            if (currentFocusIndex >= items.length) currentFocusIndex = 0;
            if (currentFocusIndex < 0) currentFocusIndex = items.length - 1;

            const targetItem = items[currentFocusIndex];
            targetItem.classList.add('bg-blue-100');
            
            // Auto-scroll inside suggestions window panel box if highlighted item rolls off viewport bounds
            targetItem.scrollIntoView({ block: 'nearest' });
        }

        function selectUser(user) {
            selectedUsers.push(user);
            searchInput.value = '';
            suggestionsBox.classList.add('hidden');
            renderSelectedUsers();
            searchInput.focus(); // Re-focus back to keep fast composition sequences intact
        }

        function unselectUser(userId) {
            selectedUsers = selectedUsers.filter(user => user.id !== userId);
            renderSelectedUsers();
        }

        function renderSelectedUsers() {
            const elements = selectedContainer.querySelectorAll('.selected-user-row');
            elements.forEach(el => el.remove());

            if (selectedUsers.length === 0) {
                placeholder.classList.remove('hidden');
                saveBtn.disabled = true;
                return;
            }

            placeholder.classList.add('hidden');
            saveBtn.disabled = false;

            selectedUsers.forEach(user => {
                const item = document.createElement('div');
                item.className = 'selected-user-row flex justify-between items-center bg-white border border-gray-200 rounded-xl p-2 shadow-xs transition hover:border-gray-300';
                item.innerHTML = `
                    <div class="text-sm">
                        <span class="font-semibold text-gray-800 block">${user.first_name}</span>
                        <span class="text-[10px] text-gray-400 uppercase">${user.department || 'General'}</span>
                    </div>
                    <input type="hidden" name="user_ids[]" value="${user.id}">
                    <button type="button" onclick="unselectUser(${user.id})" class="text-gray-400 hover:text-red-600 transition p-1 text-sm">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `;
                selectedContainer.appendChild(item);
            });
        }// Close suggestions dropdown panels on outside frame focus context changes
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.add('hidden');
            }
        });

        // Outside modal context click event handler catcher block
        function handleOutsideModalClick(e) {
            closeDistributionModal();
        }

        function openDistributionModal() {
            document.getElementById('distributionModal').classList.remove('hidden');
            setTimeout(() => searchInput.focus(), 50); // Set structural autofocus sequence hook
        }

        function closeDistributionModal() {
            document.getElementById('distributionModal').classList.add('hidden');
            selectedUsers = [];
            searchInput.value = '';
            suggestionsBox.classList.add('hidden');
            renderSelectedUsers();
        }
                    </script>
                @endif
            </div>

            <div class="w-full lg:w-1/3 flex flex-col gap-4">
                <div class="border rounded-xl p-4 bg-white overflow-y-auto h-[43vh]">
                    <h2 class="font-semibold text-lg mb-3">Review Comments ({{$reviewComments->count()}})</h2>

                    @if($reviewComments->isEmpty())
                        <div class="text-sm text-gray-500 italic">
                            No review comments for this document.
                        </div>
                    @else
                        @foreach($sectionOrder as $section)
                            @if(isset($reviewComments[$section]))
                                <div class="mb-4">
                                    <h3 class="text-sm font-bold uppercase text-gray-700 mb-2">
                                        {{ $sectionLabels[$section] }}
                                    </h3>

                                    <div class="space-y-2">
                                        @foreach($reviewComments[$section] as $comment)

                                            <div class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm">
                                                <div class="font-medium text-amber-800">
                                                    {{ $comment->user->name }}
                                                </div>

                                                <div class="text-gray-700">
                                                    {{ $comment->comment }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $comment->created_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="border rounded-xl p-4 bg-white overflow-y-auto h-[43vh]">
                    <h2 class="font-semibold text-lg mb-3">Approval Comments ({{$approvalComments->count()}})</h2>

                    @if($approvalComments->isEmpty())
                        <div class="text-sm text-gray-500 italic">
                            No approval comments for this document.
                        </div>
                    @else
                        @foreach($sectionOrder as $section)
                            @if(isset($approvalComments[$section]))
                                <div class="mb-4">
                                    <h3 class="text-sm font-bold uppercase text-gray-700 mb-2">
                                        {{ $sectionLabels[$section] }}
                                    </h3>

                                    <div class="space-y-2">
                                        @foreach($approvalComments[$section] as $comment)

                                            <div class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm">
                                                <div class="font-medium text-amber-800">
                                                    {{ $comment->user->name }}
                                                </div>

                                                <div class="text-gray-700">
                                                    {{ $comment->comment }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $comment->created_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>