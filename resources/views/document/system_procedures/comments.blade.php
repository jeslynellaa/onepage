<x-layout>
    @php
        function selected($value, $old) {
            return $value == $old ? 'selected' : '';
        }
    @endphp

    <style>
        input:read-only {
            color: gray;
            background: lightgray;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > <a href="{{ route('document.system_procedures')}}">System Procedures</a> > {{$doc->title}} > {{$pageTitle}}
        </h1>

        <div class="flex flex-col sm:flex-row w-full gap-4">
            <div id="preview-container" class="w-full md:w-1/2 h-140">
                <iframe src="{{ route('document.system_procedures.sp_preview', $doc->id) }}" class="w-full h-full"></iframe>
            </div>
            <div id="form-container" class="w-full md:w-1/2 h-140">
                <div class="px-5 py-4 w-full border rounded-md">
                    <span>
                        Write comments regarding each section
                    </span>
                    <hr>
                    <div>
                        <form action="{{ route('document.system_procedures.storeComment', $doc->id) }}" method="POST" class="py-2">
                            @csrf
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="title_comment">Title</label>
                                <input type="text" name="title_comment" value="{{ old('title_comment', $existingComments->get('title')?->comment) }}">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="objectives_comment">Objective/s</label>
                                <input type="text" name="objectives_comment" value="{{ old('objectives_comment', $existingComments->get('objectives')?->comment) }}">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="scope_comment">Scope</label>
                                <input type="text" name="scope_comment" value="{{ old('scope_comment', $existingComments->get('scope')?->comment) }}">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="flowchart_comment">Process Flowchart</label>
                                <input type="text" name="flowchart_comment" value="{{ old('flowchart_comment', $existingComments->get('flowchart')?->comment) }}">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="notes_comment">Notes</label>
                                <input type="text" name="notes_comment" value="{{ old('notes_comment', $existingComments->get('notes')?->comment) }}">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="interfaces_comment">Interfaces</label>
                                <input type="text" name="interfaces_comment" value="{{ old('interfaces_comment', $existingComments->get('interfaces')?->comment) }}">
                            </div>
                            <div class="mb-1">
                                <label class="uppercase text-xs" for="remarks_comment">Other Remarks</label>
                                <input type="text" name="remarks_comment" value="{{ old('remarks_comment', $existingComments->get('remarks')?->comment) }}">
                            </div>
                            <div class="flex justify-center gap-4 mt-4">
                                <button type="submit" name="action" value="save" class="bg-blue-300 rounded-xl px-3 py-1 cursor-pointer duration-300 hover:bg-blue-400">Save</button>
                                <button type="submit" name="action" value="send_back" class="bg-blue-300 rounded-xl px-3 py-1 cursor-pointer duration-300 hover:bg-blue-400">Send Back with Comments</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>