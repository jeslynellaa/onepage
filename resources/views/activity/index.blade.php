<x-layout>
    <div class="mx-auto px-5 pt-1 mb-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">Activity Log</h1>
        </div>

        <div class="bg-white rounded-3xl p-6">
            <table id="logs-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Performed By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="p-2">{{ $log->performed_at->format('d M Y - g:i A') }}</td>
                            <td class="p-2">{{strtoupper($log->action)}}</td>
                            <td class="p-2">{{$log->description}}</td>
                            <td class="p-2">{{$log->user->first_name}} {{$log->user->last_name}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-2">No Logs Available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <x-slot:scripts>
    <script>
        let table = new DataTable('#logs-table', {
            order: [[0, 'desc']],
            "autoWidth": false,
            "responsive": true,
            language: {
                lengthMenu: 'Show _MENU_ rows'
            },
        });
    </script>
    </x-slot:scripts>
</x-layout>