<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > System Procedures
            </h1>
            <a href="{{ route('document.system_procedures.create')}}"
            class="inline-block bg-sky-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-sky-700 transition">
                Create
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full border border-gray-200 text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                <th class="px-4 py-3 border-b">Title</th>
                <th class="px-4 py-3 border-b">Code</th>
                <th class="px-4 py-3 border-b">Section</th>
                <th class="px-4 py-3 border-b">Pages</th>
                <th class="px-4 py-3 border-b">Status</th>
                <th class="px-4 py-3 border-b">Rev. No</th>
                <th class="px-4 py-3 border-b">Effective Date</th>
                <th class="px-4 py-3 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($documents as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $doc->title }}</td>
                    <td class="px-4 py-2">{{ $doc->code }}</td>
                    <td class="px-4 py-2">{{ $doc->section_number }}</td>
                    <td class="px-4 py-2">{{ $doc->pages ?? 'Generate document first' }}</td>
                    <td class="px-4 py-2 text-green-700 font-medium">Active</td>
                    <td class="px-4 py-2">{{ $doc->revision_number }}</td>
                    <td class="px-4 py-2">{{ $doc->effective_date }}</td>
                    <td class="px-4 py-2 flex justify-center">
                        <a href="{{ route('document.system_procedures.view_pdf', $doc->id) }}"
                            class="pr-1 text-gray-600 hover:text-sky-700">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('document.system_procedures.edit', $doc->id) }}"
                            class="pr-1 text-gray-600 hover:text-sky-700">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('document.system_procedures.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pr-1 cursor-pointer transition-colors duration-300 text-gray-600 hover:text-sky-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    </div>
</x-layout>