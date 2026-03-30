<x-layout>
    <div class="mx-auto w-full px-5 pt-3">
        <div class="mb-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Document Categories</h3>
        </div>
        <div class="mx-auto w-full px-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @php
                    $categories = [
                        ['name' => 'System Procedures', 'route' => 'document.system_procedures', 'count' => $spCount],
                        ['name' => 'MS Manual', 'route' => 'document.ms_manual.index', 'count' => $msCount],
                        ['name' => 'Support Documents', 'route' => 'document.support_document.index', 'count' => $supportCount],
                        ['name' => 'Forms Manual', 'route' => 'document.forms.index', 'count' => $formsCount],
                    ];
                @endphp

                @foreach($categories as $cat)
                <a href="{{ route($cat['route']) }}" 
                class="group relative bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:border-[#575df9]/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-48">
                    
                    <div class="flex justify-between items-start">
                        <div class="h-12 w-12 bg-indigo-50 text-[#575df9] rounded-xl flex items-center justify-center text-xl group-hover:bg-[#575df9] group-hover:text-white transition-all">
                            <i class="fa-regular fa-folder"></i>
                        </div>
                        
                        <div class="text-right">
                            <span class="text-[11pt] font-black text-gray-900 block">{{ $cat['count'] ?? 0 }}</span>
                            <span class="text-[9pt] font-bold text-gray-400 uppercase tracking-tighter">Documents</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-gray-800 mb-1 group-hover:text-[#575df9] transition-colors leading-tight">
                            {{ $cat['name'] }}
                        </h3>
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#3de3b1]"></span>
                            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-tight">Active Library</p>
                        </div>
                    </div>

                    <div class="absolute bottom-4 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-arrow-right text-[#575df9] text-xs"></i>
                    </div>
                </a>
                @endforeach

            </div>
        </div>
    </div>
</x-layout>