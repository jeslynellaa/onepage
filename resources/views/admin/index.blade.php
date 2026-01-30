<x-layout>
    <style>
        table tr td:first-child{
            text-align: center;
        }
        table tr td{
            padding: 7px;
        }
        table thead tr th{
            padding: 5px;
            text-align: left;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">Admin</h1>
        </div>

        <!-- Content Row -->
        <div class="grid grid-cols-1 grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        Clients
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $clientCount}}
                    </div>
                </div>
                <i class="fa-solid fa-people-group text-3xl text-gray-400"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        {{-- Draft Documents --}}
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                    </div>
                </div>
                <i class="fa-solid fa-circle text-3xl text-gray-400"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        {{-- For Review --}}
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                    </div>
                </div>
                <i class="fa-solid fa-circle text-3xl text-gray-400"></i>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-600">
                        {{-- Pending Approval --}}
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                    </div>
                </div>
                <i class="fa-solid fa-circle text-3xl text-gray-400"></i>
            </div>

        </div>
        <div class="flex gap-5">
            <div class="overflow-x-auto bg-white rounded-2xl shadow-lg p-5 mt-3 w-3/4 max-h-100">
                <div class="flex justify-between">
                    <span class="font-semibold">OnePage Client List</span>
                    <div>
                        <a href="{{ route('admin.client.create')}}" class="hover:bg-blue-600 hover:text-white duration-300 bg-blue-300 px-3 py-2 rounded-lg cursor-pointer">Onboard New</a>
                    </div>
                </div>
                <table class="w-full mt-2 border-separate border-spacing-0 w-full">
                    <thead>
                        <tr>
                            <th class="rounded-tl-xl">#</th>
                            <th>Name</th>
                            <th>Plan</th>
                            <th class="rounded-tr-xl">Ends On</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $key => $client)
                        <tr>
                            <td class="text-left!">{{ $key+1 }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->subcription_plan ?? "-" }}</td>
                            <td>{{ $client->subscription_ends_at ?? "-"}}</td>
                            <td class="flex gap-4">
                                <a href="{{route('admin.client.view', $client->id)}}" class="" title="View Client Record">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{route('admin.client.edit', $client->id)}}" class="" title="View Client Record">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">No Data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="overflow-x-auto bg-white rounded-2xl shadow-lg px-5 py-2 mt-3 w-1/4 max-h-100">
                <span>Action Requests</span>
            </div>
        </div>

    </div>
</x-layout>