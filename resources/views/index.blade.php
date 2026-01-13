<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">Dashboard</h1>
        </div>

        <!-- Content Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        Active Documents
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{$activeCount}}
                    </div>
                </div>
                <i class="fas fa-file text-3xl text-gray-400"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        Draft Documents
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{$draftCount}}
                    </div>
                </div>
                <i class="fas fa-pen text-3xl text-gray-400"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        For Review
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{$reviewCount}}
                    </div>
                </div>
                <i class="fas fa-glasses text-3xl text-gray-400"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        Pending Approval
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{$approvalCount}}
                    </div>
                </div>
                <i class="fas fa-circle-check text-3xl text-gray-400"></i>
            </div>

        </div>
        <div class="flex gap-5">
            <div class="overflow-x-auto bg-white rounded-2xl shadow-lg px-5 py-2 mt-3 w-3/4 max-h-100">
                <table class="w-full border border-gray-200 text-sm text-left text-gray-700">
                    <thead>
                        <tr class="border border-gray-200">
                            <th class="p-2">Date</th>
                            <th class="p-2">Action</th>
                            <th class="p-2">Description</th>
                            <th class="p-2">Performed By</th>
                            <th class="p-2">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="p-2">{{ $log->performed_at->format('d M Y - g:i A') }}</td>
                                <td class="p-2">{{strtoupper($log->action)}}</td>
                                <td class="p-2">{{$log->description}}</td>
                                <td class="p-2">{{$log->user->first_name}} {{$log->user->last_name}}</td>
                                <td class="p-2"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-2">No Logs Available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="overflow-x-auto bg-white rounded-2xl shadow-lg px-5 py-2 mt-3 w-1/4 max-h-100">
                <span>Action Requests</span>
                <table class="w-full border border-gray-200 text-sm text-left text-gray-700">
                    <thead>
                        <tr class="border border-gray-200">
                            <th class="p-2">Date</th>
                            <th class="p-2">Document</th>
                            <th class="p-2">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="p-2">{{ $log->performed_at->format('d M Y - g:i A') }}</td>
                                <td class="p-2">{{strtoupper($log->action)}}</td>
                                <td class="p-2">{{$log->description}}</td>
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

    </div>
</x-layout>