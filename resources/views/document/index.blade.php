<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">Document Management</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Cards -->
            <div class="mb-3 max-w-100">
                <a href="{{ route('document.system_procedures') }}" 
                class="flex items-center py-5 px-5 bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-300 h-full">
                    <div class="text-4xl mr-2">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-md font-semibold text-gray-800 m-0 p-0">System Procedures</h3>
                        <p class="p-0 text-sm">{{ $lastActivity ? "Last activity on " . $lastActivity : "No Activity"}}</p>
                        <span class="bg-green-200 rounded-full px-3 text-sm">Active</span>
                    </div>
                </a>
            </div>
            <div class="mb-3 max-w-100">
                <a href="{{ route('document.ms_manual.index') }}" 
                class="flex items-center py-5 px-8 bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-300 h-full">
                    <div class="text-4xl mr-2">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-md font-semibold text-gray-800 m-0 p-0">MS Manual</h3>
                        <p class="p-0 text-sm">{{ $lastActivity ? "Last activity on " . $lastActivity : "No Activity"}}</p>
                        <span class="bg-green-200 rounded-full px-3 text-sm">Active</span>
                    </div>
                </a>
            </div>
            <div class="mb-3 max-w-100">
                <a href="{{ route('document.support_document.index') }}" 
                class="flex items-center py-5 px-8 bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-300 h-full">
                    <div class="text-4xl mr-2">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-md font-semibold text-gray-800 m-0 p-0">Support Documents</h3>
                        <p class="p-0 text-sm">{{ $lastActivity ? "Last activity on " . $lastActivity : "No Activity"}}</p>
                        <span class="bg-green-200 rounded-full px-3 text-sm">Active</span>
                    </div>
                </a>
            </div>
            <div class="mb-3 max-w-100">
                <a href="{{ route('document.forms.index') }}" 
                class="flex items-center py-5 px-8 bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-300 h-full" disabled>
                    <div class="text-4xl mr-2">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-md font-semibold text-gray-800 m-0 p-0">Forms Manual</h3>
                        <p class="p-0 text-sm">{{ $lastActivity ? "Last activity on " . $lastActivity : "No Activity"}}</p>
                        <span class="bg-green-200 rounded-full px-3 text-sm">Active</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-layout>