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
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > System Procedures
            </h1>
            <div>
            <a href="{{ route('document.system_procedures.create')}}"
            class="inline-block bg-sky-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-sky-700 transition">
                Create
            </a>
            <a href="{{ route('document.system_procedures.dirf_generate', 1)}}"
            class="inline-block bg-sky-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-sky-700 transition">
                DIRFs
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
                        <th class="px-4 py-3">Section Title</th>
                        <th class="px-4 py-3">Section No.</th>
                        <th class="px-4 py-3">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $section)
                    <tr class="align-middle" data-id="{{$section->section_number}}">
                        <td class="details-control dt-control"></td> <!-- Expand button -->
                        <td class="px-4 py-3">{{$section->title}}</td>
                        <td class="px-4 py-3">{{$section->section_number}}</td>
                        <td class="px-4 py-3">{{$section->count}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$totalCount}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <x-slot:scripts>
        <script>
            window.canViewRevisionHistory = @json(auth()->user()->role === 'Document Controller');
            window.canReview = @json(auth()->user()->role === 'Reviewer');
            window.canApprove = @json(auth()->user()->role === 'Approver');
            window.canSend = @json(auth()->user()->role === 'User');
            const loadedSections = {};

            let table = new DataTable('#sections-table', {
                order: [[2, 'asc']],
                "autoWidth": false,
                "responsive": true,
                columnDefs: [
                    {targets: [0], orderable: false}
                ],
                "paging": false,
                "searching": false
            });

            $('#sections-table tbody').on('click', 'td.details-control', function () {
                const tr = $(this).closest('tr');
                const row = table.row(tr);
                const sectionId = tr.data('id');
                console.log(sectionId)

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
                                        window.canViewRevisionHistory
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
                        if(details.status !== 'For Review' && details.status !== 'For Approval' ){
                            itemsTable += `
                                <a href="${details.editUrl}" class="text-gray-600 hover:text-sky-700">
                                    <i class="fa-solid fa-pen"></i>
                                </a>`;
                        }
                        itemsTable += `
                                <form action="${details.deleteUrl}" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this document?\');" style="display:inline;">
                                    <input type="hidden" name="_token" value="${data.csrf}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-gray-600 hover:text-sky-700 cursor-pointer transition-colors duration-300">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>`;
                        if(details.status === 'Draft' && window.canSend){
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
                        }else if(details.status === 'For Review' && window.canReview){
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
                            `;
                        }else if(details.status === 'For Approval' && window.canApprove){
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
                if (!dateString) return '-';
                
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
                    console.log(tr.data('id'));

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
        </script>
    </x-slot:scripts>
</x-layout>