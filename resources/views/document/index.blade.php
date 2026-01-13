<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-semibold text-gray-800">Document Management</h1>
        </div>

        <div class="flex flex-wrap -mx-2">
            <!-- Cards -->
            <div class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3 h-32">
                <a href="{{ route('document.system_procedures') }}" 
                class="flex items-center py-5 px-8 bg-white rounded-3xl shadow hover:shadow-lg transition-all duration-300 h-full">
                    <div class="text-5xl mr-5">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-800 m-0 p-0">System Procedures</h3>
                        <p class="p-0 text-sm">Last activity on {{$lastActivity}}</p>
                        <span class="bg-green-200 rounded-md px-3 text-sm">Active</span>
                    </div>
                </a>
            </div>
            {{-- <div class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3 h-32">
                <a href="{{ route('document.system_procedures') }}" 
                class="flex items-center py-5 px-8 bg-white rounded-3xl shadow hover:shadow-lg transition-all duration-300 h-full">
                    <div class="text-5xl mr-5">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-800 m-0 p-0">Management System</h3>
                        <p class="p-0 text-sm">Updated a week ago</p>
                        <span class="bg-green-200 rounded-md px-3 text-sm">Active</span>
                    </div>
                </a>
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3 h-32">
                <a href="{{ route('document.system_procedures') }}" 
                class="flex items-center py-5 px-8 bg-white rounded-3xl shadow hover:shadow-lg transition-all duration-300 h-full">
                    <div class="text-5xl mr-5">
                        <i class="fa-regular fa-file"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-800 m-0 p-0">Support Documents</h3>
                        <p class="p-0 text-sm">Updated a week ago</p>
                        <span class="bg-green-200 rounded-md px-3 text-sm">Active</span>
                    </div>
                </a>
            </div> --}}
        </div>
    </div>
</x-layout>