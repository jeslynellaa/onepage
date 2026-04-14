<x-layout>
    <div class="max-w-4xl mx-auto py-4 px-6">
        <header class="mb-5">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">System Settings</h1>
            <p class="text-gray-500 mt-2">Manage your company branding and document generation defaults.</p>
        </header>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md border border-gray-100 rounded-2xl overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-10">
                <section>
                    <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-6">Company Identity</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                            <div class="relative group border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-colors hover:border-indigo-300">
                                @if($company->logo_path)
                                    <img src="{{ asset('storage/' . $company->logo_path) }}" id="logo_preview_src" alt="Logo Preview" class="w-full h-32 object-contain rounded-lg mb-3">
                                    <span id="logo_placeholder" class="hidden text-gray-300 italic text-xs">Logo Preview</span>
                                @else
                                    <img src="" id="logo_preview_src" class="hidden w-full h-32 object-contain rounded-lg mb-3">
                                    <div id="logo_placeholder" class="w-full h-32 bg-gray-50 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <input type="file" name="logo" id="logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <p class="text-[10px] text-center text-gray-400 uppercase font-bold tracking-tighter">Click to Upload</p>
                            </div>
                        </div>

                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" name="company_name" id="company_name" value="{{ $company->name }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 text-sm">
                            </div>

                            <div>
                                <label for="brand_color_picker" class="block text-sm font-medium text-gray-700 mb-2">Brand Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="brand_color_picker" name="brand_color" value="{{ $company->hex_code ?? '#4F46E5' }}" class="h-10 w-14 border border-gray-200 rounded-lg cursor-pointer p-1">
                                    <span class="text-xs text-gray-400 font-mono" id="hex_label">{{ $company->hex_code ?? '#4F46E5' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100">

                <section>
                    <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-6">Document Defaults</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="paper_size" class="block text-sm font-medium text-gray-700 mb-2">Default Paper Size</label>
                            <select name="paper_size" id="paper_size" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 text-sm">
                                <option value="a4" {{ $company->paper_size == 'a4' ? 'selected' : '' }}>A4 (International)</option>
                                <option value="letter" {{ $company->paper_size == 'letter' ? 'selected' : '' }}>Letter (US)</option>
                                <option value="legal" {{ $company->paper_size == 'legal' ? 'selected' : '' }}>Legal</option>
                            </select>
                        </div>

                        <div>
                            <label for="pdf_font" class="block text-sm font-medium text-gray-700 mb-2">Primary PDF Font</label>
                            <select name="pdf_font" id="pdf_font" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 text-sm">
                                <option value="Helvetica" {{ $company->pdf_font == 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                <option value="Times-Roman" {{ $company->pdf_font == 'Times-Roman' ? 'selected' : '' }}>Times New Roman</option>
                                <option value="Courier" {{ $company->pdf_font == 'Courier' ? 'selected' : '' }}>Courier</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            <div class="bg-gray-50/50 px-8 py-6 border-t border-gray-100 flex justify-end items-center gap-6">
                <span class="text-xs text-gray-400 max-w-xs text-right">Settings are applied globally. Changes will reflect on all new PDF generations.</span>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-sm font-semibold shadow-md shadow-indigo-100 transition-all active:scale-95">
                    Save Changes
                </button>
            </div>
        </form>

        <div class="mt-12">
            <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-2">Document Header Preview</h2>
            <div id="preview-container" class="bg-white border border-gray-200 rounded-2xl p-8 shadow overflow-hidden" style="font-family: {{$company->pdf_font}};">
                <header class="w-full">
                    <table id="header_table" class="w-full border-collapse text-left" style="border: 1px solid black;">
                        <tbody>
                            <tr id="manual_name_preview" style="background-color: {{ $company->hex_code ?? '#4F46E5' }}; color: white;">
                                <td colspan="3" class="border border-black p-1 text-center font-bold text-[11pt]">
                                    SYSTEM PROCEDURES MANUAL
                                </td>
                            </tr>
                            <tr>
                                <td rowspan="5" class="border border-black p-4 text-center align-middle w-[60%]">
                                    <div class="flex items-center justify-center h-[90px]">
                                        <img id="header_logo_preview" src="{{ $company->logo_path ? asset('storage/'.$company->logo_path) : '' }}" class="max-h-full max-w-full {{ $company->logo_path ? '' : 'hidden' }}">
                                        <div id="header_logo_placeholder" class="text-gray-300 italic text-xs {{ $company->logo_path ? 'hidden' : '' }}">LOGO</div>
                                    </div>
                                </td>
                                <td class="border border-black py-[1px] px-2 text-[11pt] w-[20%]">Section No.:</td>
                                <td class="border border-black py-[1px] px-2 text-[11pt] w-[20%]">01</td>
                            </tr>
                            <tr>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">Revision No.:</td>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">0</td>
                            </tr>
                            <tr>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">Document No.:</td>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">SP-BPL-01</td>
                            </tr>
                            <tr>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">Effective Date:</td>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">{{ date('m/d/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">Page Number:</td>
                                <td class="border border-black py-[1px] px-2 text-[11pt]">Page 1 of 1</td>
                            </tr>
                            <tr id="title_row_preview" class="uppercase font-bold text-[14pt] text-center bg-white text-black">
                                <td colspan="3" class="border border-black p-1">SAMPLE DOCUMENT TITLE</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-[9pt] italic text-center">
                        <strong>STRICTLY CONFIDENTIAL</strong> - For use of <span id="preview_confidential_name">{{ $company->name ?? 'Company' }}</span> only. Unauthorized reproduction is strictly prohibited.
                    </div>
                </header>
            </div>
        </div>
    </div>

    <x-slot:scripts>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const companyInput = document.getElementById('company_name');
            const colorPicker = document.getElementById('brand_color_picker');
            const hexLabel = document.getElementById('hex_label');
            const fontSelect = document.getElementById('pdf_font');
            const logoInput = document.getElementById('logo');

            // Update Text (Live)
            companyInput.addEventListener('input', (e) => {
                const val = e.target.value.trim() || 'COMPANY NAME';
                document.getElementById('preview_company_name').innerText = val.toUpperCase();
                document.getElementById('preview_confidential_name').innerText = val;
            });

            // Update Color & Handle Contrast
            const updateColor = (color) => {
                const headerRow = document.getElementById('manual_name_preview');
                headerRow.style.backgroundColor = color;
                hexLabel.innerText = color.toUpperCase();
                
                // YIQ Contrast Calculation
                const r = parseInt(color.substring(1,3), 16);
                const g = parseInt(color.substring(3,5), 16);
                const b = parseInt(color.substring(5,7), 16);
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                headerRow.style.color = (yiq >= 128) ? 'black' : 'white';
            };

            colorPicker.addEventListener('input', (e) => {
                updateColor(e.target.value);
            });

            // Update Font Family
            fontSelect.addEventListener('change', (e) => {
                const container = document.getElementById('preview-container');
                const fontMap = {
                    'Helvetica': 'sans-serif',
                    'Times-Roman': 'serif',
                    'Courier': 'monospace'
                };
                container.style.fontFamily = fontMap[e.target.value] || 'sans-serif';
            });

            // Logo Preview (FileReader)
            logoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Update the upload box preview
                        const uploadPreview = document.getElementById('logo_preview_src');
                        const uploadPlaceholder = document.getElementById('logo_placeholder');
                        uploadPreview.src = e.target.result;
                        uploadPreview.classList.remove('hidden');
                        uploadPlaceholder.classList.add('hidden');

                        // Update the table header preview
                        const headerLogo = document.getElementById('header_logo_preview');
                        const headerPlaceholder = document.getElementById('header_logo_placeholder');
                        headerLogo.src = e.target.result;
                        headerLogo.classList.remove('hidden');
                        headerPlaceholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    </x-slot:scripts>
</x-layout>