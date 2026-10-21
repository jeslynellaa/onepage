<x-layout>
    @php
        $isNewRevision = $doc->status === 'Active';
        $currentSection = old('section_id', $doc->section_id);
    @endphp
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > <a href="{{ route('document.forms.index') }}">Forms Manual</a> > {{ $isNewRevision ? 'New Revision' : 'Edit' }}
        </h1>

        @if($isNewRevision)
            <div class="mt-2 rounded-md bg-blue-50 border border-blue-100 px-4 py-2 text-sm text-blue-800">
                This form is Active. Saving will create a new revision draft of <strong>{{ $doc->code }}</strong>; the current Active revision stays in force until the new one is approved.
            </div>
        @endif

        <div class="shadow-md rounded-lg bg-white p-5 mt-2">
            <form id="documentForm" method="POST" action="{{ route('document.forms.update', $doc->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-6">
                        <label for="title" class="block text-xs font-bold uppercase mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $doc->title) }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="section_id" class="block text-xs font-bold uppercase mb-1">Section No.</label>
                        <select name="section_id" id="section_id" class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                            @foreach($process_names as $process)
                                <option value="{{$process->id}}" {{ $process->id == $currentSection ? 'selected' : '' }}>{{$process->section_number}} {{$process->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="code" class="block text-xs font-bold uppercase mb-1">Code</label>
                        <input type="text" id="code" name="code" value="{{ old('code', $doc->code) }}" @readonly($isNewRevision)
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pages" class="block text-xs font-bold uppercase mb-1">Pages</label>
                        <input type="text" id="pages" name="pages" value="{{ old('pages', $doc->pages) }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="justification" class="block text-xs font-bold uppercase mb-1">Justification/ Objective</label>
                    <input required type="text" id="justification" name="justification" placeholder="Explain why document is created or modified" value="{{ old('justification', $isNewRevision ? '' : $doc->justification) }}"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                </div>

                <hr class="border-gray-300">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label for="file" class="block text-xs font-bold uppercase mb-1">PDF Copy</label>
                        <p class="text-xs text-gray-500 mb-2">
                            Current file:
                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-sky-700 hover:underline">view PDF</a>.
                            Leave empty to keep it.
                        </p>
                        <input type="file" id="file" name="file" accept="application/pdf" class="block w-full text-sm text-gray-700
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-200 file:text-gray-700
                                hover:file:bg-gray-300" />
                    </div>
                </div>
            </form>

            <div class="text-center mt-6 mb-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition" form="documentForm">
                    {{ $isNewRevision ? 'Create Revision Draft' : 'Save Changes' }}
                </button>
            </div>
        </div>
    </div>
</x-layout>
