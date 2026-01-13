<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > System Procedures > Revision History
            </h1>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-2xl shadow-lg px-5 py-2">
            <table id="history-table" class="w-full border border-gray-200 text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Revision No.</th>
                        <th class="px-4 py-3">Effective Date</th>
                        <th class="px-4 py-3 text-center!">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($revhistory as $history)
                    <tr class="align-middle">
                        <td class="px-4 py-3">{{$history->title}}</td>
                        <td class="px-4 py-3">{{$history->status}}</td>
                        <td class="px-4 py-3">{{$history->revision_number ?? '-'}}</td>
                        <td class="px-4 py-3">{{$history->effective_date ?? '-'}}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('document.system_procedures.view_pdf', $history->id)}}" class="text-gray-600 hover:text-sky-700">
                                <i class="fa-solid fa-eye"></i>
                            </a>
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
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        let table = new DataTable('#history-table', {
            order: [[2, 'asc']],
            "autoWidth": false,
            "responsive": true,
            columnDefs: [
                {targets: [0], orderable: false}
            ],
            "paging": false,
            "searching": false
        });
    </script>
</x-layout>