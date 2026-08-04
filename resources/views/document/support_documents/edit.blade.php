<x-layout>
    @php
        function selected($value, $old) {
            return $value == $old ? 'selected' : '';
        }
    @endphp
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > Support Documents > Edit
        </h1>

        <div class="shadow-md rounded-lg bg-white p-5 mt-2">
            <form id="documentForm" method="POST" 
                action="{{ isset($doc) && $doc->status !== 'Active' ? route('document.support_document.update', $doc->id) : route('document.support_document.store') }}" 
                enctype="multipart/form-data" class="space-y-8"
            >
                @csrf
                @if(isset($doc) && $doc->status !== 'Active')
                    @method('PUT')
                @endif
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-6">
                        <label for="title" class="block text-xs font-bold uppercase mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') ?? $doc->title }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="section_id" class="block text-xs font-bold uppercase mb-1">Section No.</label>
                        <select name="section_id" id="section_id" value="{{ old('section_id') ?? $doc->section_id }}" class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                            <option disabled selected>-- Select --</option>
                            <option value="1" {{ selected('1', old('section_id')) }}>01 Business Planning</option>
                            <option value="2" {{ selected('2', old('section_id')) }}>02 Business Development</option>
                            <option value="3" {{ selected('3', old('section_id')) }}>03 Project Planning and Implementation</option>
                            <option value="4" {{ selected('4', old('section_id')) }}>04 Project Evaluation</option>
                            <option value="5" {{ selected('5', old('section_id')) }}>05 Project Completion</option>
                            <option value="6" {{ selected('6', old('section_id')) }}>06 Asset Management</option>
                            <option value="7" {{ selected('7', old('section_id')) }}>07 Maintenance</option>
                            <option value="8" {{ selected('8', old('section_id')) }}>08 Human Resource Management</option>
                            <option value="9" {{ selected('9', old('section_id')) }}>09 Financial Resource Management</option>
                            <option value="10" {{ selected('10', old('section_id')) }}>10 Documented Information Management</option>
                            <option value="11" {{ selected('11', old('section_id')) }}>11 Continual Improvement</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="code" class="block text-xs font-bold uppercase mb-1">Code</label>
                        <input type="text" id="code" name="code" value="{{ old('code') ?? $doc->code }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pages" class="block text-xs font-bold uppercase mb-1">Pages</label>
                        <input type="text" id="pages" name="pages" value="{{ old('pages') ?? $doc->pages }}"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="justification" class="block text-xs font-bold uppercase mb-1">Justification/ Objective</label>
                    <input required type="text" id="justification" name="justification" placeholder="Explain why document is created or modified" value="{{ old('justification') ?? $doc->justification }}"
                        class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2" />
                </div>
                
                <hr class="border-gray-300">
                
                <!-- Objective & Scope -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="objective" class="block text-xs font-bold uppercase mb-1">Objective</label>
                        <textarea id="objective" name="objective" rows="5"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">{{ old('objective') ?? $doc->objective }}</textarea>
                    </div>
                    <div>
                        <label for="scope" class="block text-xs font-bold uppercase mb-1">Scope</label>
                        <textarea id="scope" name="scope" rows="5"
                            class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">{{ old('scope') ?? $doc->scope }}</textarea>
                    </div>
                    
                    <div class="col-span-full">
                        <label for="file" class="block text-xs font-bold uppercase mb-1">PDF Copy</label>
                        
                        <!-- 1. Show the existing file if it exists -->
                        @if(isset($doc) && $doc->file_path)
                            <div class="mb-3 p-3 bg-gray-50 border border-gray-200 rounded-md flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2 text-gray-600">
                                    <!-- Simple PDF Icon placeholder -->
                                    <svg class="w-5 height-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>
                                    <span class="font-medium truncate max-w-xs md:max-w-md">
                                        Current File: {{ basename($doc->file_path) }}
                                    </span>
                                </div>
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                    View PDF
                                </a>
                            </div>
                        @endif

                        <!-- 2. The file input (Notice: 'required' is conditional now) -->
                        <input type="file" id="file" name="file" accept="application/pdf" 
                            class="block w-full text-sm text-gray-700
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-200 file:text-gray-700
                                hover:file:bg-gray-300"
                            {{ (isset($doc) && $doc->file_path)? '' : 'required' }} />
                            
                        @if(isset($doc) && $doc->file_path)
                            <p class="text-xs text-gray-500 mt-1">Leave blank if you want to keep the current document.</p>
                        @endif
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