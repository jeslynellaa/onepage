<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > <a href="{{ route('document.ms_manual.index') }}">Management System Manual</a> > <span class="uppercase">{{$doc->title}}</span>
            </h1>
        </div>
        {{-- MsManualController has no review/approval actions yet, so only edit/archive are offered. --}}
        @php($user = auth()->user())
        <x-document-actions class="mb-4"
            :label="$doc->title"
            :open-url="$doc->file_path ? Storage::url($doc->file_path) : null"
            :edit-url="! in_array($doc->status, ['For Review', 'For Approval'], true) && $user->can('update', $doc) ? route('document.ms_manual.edit', $doc) : null"
            :archive-url="$user->can('delete', $doc) ? route('document.ms_manual.destroy', $doc) : null"
        />
        <div class="bg-white shadow rounded-3xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 w-full">
                <div class="md:col-span-4">
                    <div class="text-xs uppercase font-bold">Section & Title</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->section_number }} - {{ $doc->title }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs uppercase font-bold">Pages</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->pages }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs uppercase font-bold">Effective Date</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->effective_date ?? "N/A"}}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs uppercase font-bold">Revision Number</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->revision_number ?? "N/A"}}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs uppercase font-bold">Status</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200 text-center">{{ $doc->status }}</div>
                </div>
                <div class="col-span-full">
                    <div class="text-xs uppercase font-bold">Justification</div>
                    <div class="px-4 rounded-full py-1 bg-blue-200">{{ $doc->justification }}</div>
                </div>
            </div>
            <iframe src="{{ Storage::url($doc->file_path) }}" class="w-full h-screen mt-4"></iframe>
        </div>
    </div>
</x-layout>