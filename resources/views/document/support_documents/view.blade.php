<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > Management System Manual > <span class="uppercase">{{$doc->title}}</span>
            </h1>
        </div>
        <div class="bg-white shadow rounded-3xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 w-full">
                <div class="md:col-span-8">
                    <div class="text-xs uppercase font-bold">Title</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->title }}</div>
                </div>
                <div class="md:col-span-4">
                    <div class="text-xs uppercase font-bold">Code</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->code }}</div>
                </div>
                <div class="md:col-span-3">
                    <div class="text-xs uppercase font-bold">Pages</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->pages }}</div>
                </div>
                <div class="md:col-span-3">
                    <div class="text-xs uppercase font-bold">Effective Date</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->effective_date ?? "N/A"}}</div>
                </div>
                <div class="md:col-span-3">
                    <div class="text-xs uppercase font-bold">Revision Number</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->revision_number ?? "N/A"}}</div>
                </div>
                <div class="md:col-span-3">
                    <div class="text-xs uppercase font-bold">Status</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->status }}</div>
                </div>
                <div class="col-span-full">
                    <div class="text-xs uppercase font-bold">Justification</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200">{{ $doc->justification }}</div>
                </div>
            </div>
            <iframe src="{{ asset('storage/'.$doc->file_path) }}" class="w-full h-screen mt-4"></iframe>
        </div>
    </div>
</x-layout>