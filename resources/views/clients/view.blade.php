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
                        <td colspan="6" class="italic text-center">No Users</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="shadow-md rounded-xl bg-white p-5 mt-4">
            <div class="flex justify-between mb-3">
                <div class="font-bold mb-2">
                    Registration Invites
                </div>
                <div x-data="{ openInviteModal: {{ $errors->any() ? 'true' : 'false' }} }">

                    <button @click="openInviteModal = true" class="hover:bg-blue-600 hover:text-white duration-300 bg-blue-300 px-3 py-2 rounded-lg cursor-pointer">
                        Invite New
                    </button>
                    <!-- Modal Background -->
                    <div x-show="openInviteModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <div @click.away="openInviteModal = false" class="bg-white rounded-xl shadow-lg w-full max-w-md p-6" >
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="font-bold text-lg">Invite User</h2>
                                <button @click="openInviteModal = false" class="text-gray-500 hover:text-black">
                                    ✕
                                </button>
                            </div>

                            <form method="POST" action="{{ route('admin.client.invite', $client->id) }}" class="space-y-4">
                                @csrf

                                @if ($errors->any())
                                    <div class="mb-4 p-2 bg-red-100 text-red-700 rounded">
                                        <ul class="list-disc pl-5">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium mb-1">Email</label>
                                    <input type="email" name="email" required class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-200">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Role</label>
                                    <select name="role" required class="w-full border rounded-lg p-2">
                                        <option value="">Select role</option>
                                        <option value="Admin">Admin</option>
                                        <option value="User">User</option>
                                        <option value="Document Controller">Document Controller</option>
                                    </select>
                                </div>

                                <div class="flex justify-end gap-2 pt-3">
                                    <button type="button" @click="openInviteModal = false" class="px-4 py-2 rounded-lg border">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600">
                                        Save Invite
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-300">
                        <th class="text-left rounded-tl-xl">#</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Role</th>
                        <th class="rounded-tr-xl"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invitations as $key => $invitation)
                    <tr class="p-2">
                        <td>{{$key+1}}</td>
                        <td>{{$invitation->email}}</td>
                        <td class="capitalize">{{$invitation->role}}</td>
                        <td x-data="{ sending: false }">
                            <button type="button" @click="sendInvite()" :disabled="sending" class="text-blue-500 hover:text-blue-700 cursor-pointer duration-300" {{ $invitation->sent_out ? 'disabled' : ''}}>
                                <i class="fa-solid fa-envelope" :class="{ 'opacity-50': sending }"></i>
                            </button>

                            <script>
                                function sendInvite() {
                                    this.sending = true;

                                    fetch("{{ route('admin.client.send-invite', $invitation->id) }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        this.sending = false;

                                        if (data.success) {
                                            alert('Invitation email sent!');
                                        } else {
                                            alert(data.message ?? 'Something went wrong.');
                                        }
                                    })
                                    .catch(() => {
                                        this.sending = false;
                                        alert('Failed to send invitation.');
                                    });
                                }
                            </script>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="italic text-center">No Pending Invites</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>