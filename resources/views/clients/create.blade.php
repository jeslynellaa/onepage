<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('admin.index') }}">Admin </a> > Clients > Onboard New
        </h1>

        <div class="shadow-md rounded-xl bg-white p-5 mt-2">
            <form id="companyForm" method="POST" action="{{ route('admin.client.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="name">Company Name</label>
                        <input type="text" name="name" required/>
                    </div>
                    <div>
                        <label for="subscription_plan">Subscription Plan</label>
                        <select name="subscription_plan" id="subscription_plan">
                            <option value="">-- Select --</option>
                            <option value="Trial">Trial</option>
                            <option value="Starter">Starter</option>
                        </select>
                    </div>
                    <div>
                        <label for="subscription_status">Subscription Status</label>
                        <select name="subscription_status" id="subscription_status">
                            <option value="">-- Select --</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-4">
                        <label for="hex_code">Theme Color</label>
                        <div class="flex gap-2 items-center">
                            <input type="color" id="hex_code" name="hex_code" class="w-14! h-13 p-0 border rounded cursor-pointer">
                            <span class="text-sm">This will be the color theme for the company documents.</span>
                        </div>
                    </div>
                    <div class="md:col-span-8">
                        <label for="logo" class="block font-medium mb-1">
                            Logo (PNG only)
                        </label>
                        <input type="file" name="logo" id="logo" accept="image/png" class="block w-full text-sm text-gray-700
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-100 file:text-gray-700
                                hover:file:bg-gray-200"
                            required />
                    </div>
                </div>
                
                <button type="submit" class="rounded-xl bg-blue-400 hover:bg-blue-700 hover:text-white p-3 block mx-auto justify-center duration-300 cursor-pointer">Submit</button>
            </form>
        </div>

    </div>
</x-layout>