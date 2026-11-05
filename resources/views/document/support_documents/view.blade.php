<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">
                <a href="{{ route('document.index') }}">Document Management</a> > <a href="{{ route('document.support_document.index') }}">Support Documents</a> > <span class="uppercase">{{$doc->title}}</span>
            </h1>
        </div>
        @php($user = auth()->user())
        <x-document-actions class="mb-4"
            :label="$doc->code ?: $doc->title"
            :open-url="$doc->file_path ? asset('storage/'.$doc->file_path) : null"
            :edit-url="$canEdit ? route('document.support_document.edit', $doc) : null"
            :edit-label="$doc->status === 'Active' ? 'Revise' : 'Edit'"
            :history-url="$doc->code && $user->can('viewRevisionHistory', $doc) ? route('document.support_document.rev_history', $doc->code) : null"
            :send-url="$user->can('sendForReview', $doc) ? route('document.support_document.forReview', $doc) : null"
            :review-url="$user->can('review', $doc) ? route('document.support_document.reviewPassOrFail', $doc) : null"
            :approve-url="$user->can('approve', $doc) ? route('document.support_document.approveOrNot', $doc) : null"
            :assign-code-url="$user->can('setCode', $doc) ? route('document.support_document.assignCode', $doc) : null"
            :suggested-code="$doc->code"
            :suggested-revision="$doc->revision_number"
            :archive-url="$user->can('delete', $doc) ? route('document.support_document.destroy', $doc) : null"
        />
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