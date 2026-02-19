<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > Management System Manual > Upload New
        </h1>

        <div class="shadow-md rounded-lg bg-white p-5 mt-2">
            <form id="documentForm" method="POST" action="{{ route('document.ms_manual.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-6">
                        <label for="title" class="block text-xs font-bold uppercase mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="md:col-span-3">
                        <label for="section_number" class="block text-xs font-bold uppercase mb-1">Section No.</label>
                        <input type="text" id="section_number" name="section_number" value="{{ old('section_number') }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="md:col-span-3">
                        <label for="pages" class="block text-xs font-bold uppercase mb-1">Pages</label>
                        <input type="text" id="pages" name="pages" value="{{ old('pages') }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="col-span-full">
                        <label for="justification" class="block text-xs font-bold uppercase mb-1">Justification/ Objective</label>
                        <input required type="text" id="justification" name="justification" placeholder="Explain why document is created or modified" value="{{ old('justification') }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>
                
                    <hr class="border-gray-300 col-span-full">

                    <div class="col-span-full">
                        <label for="file" class="block text-xs font-bold uppercase mb-1">PDF Copy</label>
                        <input required type="file" id="file" name="file" value="{{ old('file') }}" accept="application/pdf" class="block w-full text-sm text-gray-700
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-200 file:text-gray-700
                                hover:file:bg-gray-300"
                            required />
                    </div>
                </div>
            </form>

            <div class="text-center mt-6 mb-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition" form="documentForm">
                    Submit
                </button>
            </div>
        </div>
    </div>
</x-layout>