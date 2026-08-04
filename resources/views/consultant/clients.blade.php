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
            <h1 class="font-semibold text-gray-800">My Clients</h1>
        </div>

        <div class="overflow-x-auto bg-white rounded-2xl shadow-lg p-5 mt-3">
            <span class="font-semibold">Assigned Clients</span>
            <table class="w-full mt-2 border-separate border-spacing-0 w-full">
                <thead>
                    <tr>
                        <th class="rounded-tl-xl">#</th>
                        <th>Client</th>
                        <th>Assigned Since</th>
                        <th class="rounded-tr-xl"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $key => $assignment)
                    <tr>
                        <td class="text-left!">{{ $key+1 }}</td>
                        <td>{{ $assignment->company->name }}</td>
                        <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('consultant.clients.enter', $assignment->company_id) }}">
                                @csrf
                                <button type="submit" class="hover:bg-blue-600 hover:text-white duration-300 bg-blue-300 px-3 py-2 rounded-lg cursor-pointer">
                                    Enter
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">You have not been assigned to any clients yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
