<x-layout>
    <div class="mx-auto px-5 pt-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">Dashboard</h1>
        </div>

        <!-- Content Row -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between flex-col-reverse sm:flex-row">
                <div class="flex flex-col-reverse sm:flex-col">
                    <div class="text-sm font-semibold text-gray-600">
                        Active Documents
                    </div>
                    <div class="text-2xl font-bold text-gray-900 text-center sm:text-left">
                        {{$activeCount}}
                    </div>
                </div>
                <i class="fas fa-file text-lg sm:text-3xl text-[#74c365]"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between flex-col-reverse sm:flex-row">
                <div class="flex flex-col-reverse sm:flex-col">
                    <div class="text-sm font-semibold text-gray-600">
                        Draft Documents
                    </div>
                    <div class="text-2xl font-bold text-gray-900 text-center sm:text-left">
                        {{$draftCount}}
                    </div>
                </div>
                <i class="fas fa-pen text-lg sm:text-3xl text-[#74c365]"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between flex-col-reverse sm:flex-row">
                <div class="flex flex-col-reverse sm:flex-col">
                    <div class="text-sm font-semibold text-gray-600">
                        For Review
                    </div>
                    <div class="text-2xl font-bold text-gray-900 text-center sm:text-left">
                        {{$reviewCount}}
                    </div>
                </div>
                <i class="fas fa-glasses text-lg sm:text-3xl text-[#74c365]"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between flex-col-reverse sm:flex-row">
                <div class="flex flex-col-reverse sm:flex-col">
                    <div class="text-sm font-semibold text-gray-600">
                        Pending Approval
                    </div>
                    <div class="text-2xl font-bold text-gray-900 text-center sm:text-left">
                        {{$approvalCount}}
                    </div>
                </div>
                <i class="fas fa-circle-check text-lg sm:text-3xl text-[#74c365]"></i>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 my-6">
            <div class="md:col-span-8 bg-white rounded-2xl shadow-md p-5 max-h-100 flex flex-col">
                <div class="flex justify-between border-b border-gray-300 items-center mb-2 pt-1 pb-3">
                    <div class="font-semibold">Activity Log</div>
                    <div>
                        <a href="#" class="cursor-pointer duration-300 text-[#001f3f] bg-blue-200 rounded-xl py-2 px-3 hover:text-white hover:bg-[#1e488f]">View all</a>
                    </div>
                </div>
                <div class="overflow-y-auto max-h-96">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead>
                            <tr class="">
                                <th class="p-2 sticky top-0 bg-white z-10">Date</th>
                                <th class="p-2 sticky top-0 bg-white z-10">Action</th>
                                <th class="p-2 sticky top-0 bg-white z-10">Description</th>
                                <th class="p-2 sticky top-0 bg-white z-10">Performed By</th>
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
            <div class="md:col-span-4 bg-white rounded-2xl shadow-md p-5 max-h-100 flex flex-col">
                <div class="flex justify-between border-b border-gray-300 items-center mb-2 pt-1 pb-3">
                    <div class="font-semibold">Action Requests</div>
                    <div>
                    </div>
                </div>
                <div class="overflow-y-auto max-h-96">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead>
                            <tr class="">
                                <th class="p-2 sticky top-0 bg-white z-10">Date</th>
                                <th class="p-2 sticky top-0 bg-white z-10">Document</th>
                                <th class="p-2 sticky top-0 bg-white z-10">View</th>
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
    </div>
</x-layout>