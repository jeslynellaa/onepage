<x-layout>
    <style>
        .logo-frame {
            width: 200px;
            height: 200px;      /* square frame */
            background: #f3f4f6; /* optional neutral background */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        table thead tr th,
        table tbody tr td{
            padding: 5px;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('admin.index') }}">Admin </a> > Clients > View
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-4 shadow-md rounded-xl bg-white p-5 mt-2">
                <div class="logo-frame justify-center mx-auto">
                    <img src="{{ asset('storage/' . $client->logo_path) }}"
                        alt="{{ $client->name }} logo"
                        style="width: full">
                </div>
            </div>

            <div class="md:col-span-8 shadow-md rounded-xl bg-white p-5 mt-2">
                <div class="font-bold py-3">
                    General Information
                </div>
                <div>
                    <div class="flex items-center w-full gap-2 mb-3">
                        <span class="whitespace-nowrap">Name:</span>
                        <span class="font-semibold flex-1 bg-gray-200 rounded-xl px-3 py-1">
                            {{ $client->name }}
                        </span>
                    </div>
                    <div class="flex items-center w-full gap-2 mb-3">
                        <span class="whitespace-nowrap">Subscription Plan:</span>
                        <span class="font-semibold flex-1 bg-gray-200 rounded-xl px-3 py-1">
                            {{ $client->subscription_plan }}
                        </span>
                    </div>
                    <div class="flex items-center w-full gap-2 mb-3">
                        <span class="whitespace-nowrap">Subscription Status:</span>
                        <span class="font-semibold flex-1 bg-gray-200 rounded-xl px-3 py-1">
                            {{ $client->subscription_status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="shadow-md rounded-xl bg-white p-5 mt-4">
            <div class="font-bold mb-2">
                User Accounts
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-300">
                        <th class="text-left rounded-tl-xl">#</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Role</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th class="rounded-tr-xl"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userAccounts as $key => $user)
                    <tr class="p-2">
                        <td>{{$key+1}}</td>
                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                        <td class="capitalize">{{$user->role}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->username}}</td>
                        <td></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">No Users</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="shadow-md rounded-xl bg-white p-5 mt-4">
            <div class="font-bold mb-2">
                Registration Invites
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-300">
                        <th class="text-left rounded-tl-xl">#</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Role</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th class="rounded-tr-xl"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userAccounts as $key => $user)
                    <tr class="p-2">
                        <td>{{$key+1}}</td>
                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                        <td class="capitalize">{{$user->role}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->username}}</td>
                        <td></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">No Users</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>