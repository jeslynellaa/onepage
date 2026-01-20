<x-layout>
    <style>
        input:read-only {
            color: gray;
            background: lightgray;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            Edit User Profile
        </h1>
        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="shadow-md rounded-lg bg-white p-5 mt-2">
            <form method="POST" action="{{ route('profile.update', $user->id) }}"  enctype="multipart/form-data" class="space-y-6" >
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="first_name">Given Name</label>
                        <input type="text" name="first_name" value="{{$user->first_name ?? old('first_name')}}" required/>
                    </div>
                    <div>
                        <label for="middle_name">Middle Name</label>
                        <input type="text" name="middle_name" value="{{$user->middle_name ?? old('middle_name')}}" required/>
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" value="{{$user->last_name ?? old('last_name')}}" required/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="username">User Name</label>
                        <input type="text" name="username" value="{{$user->username ?? old('username')}}" required/>
                    </div>
                    <div>
                        <label for="">Email</label>
                        <input type="email" name="email" value="{{$user->email ?? old('email')}}" required/>
                    </div>
                </div>
                <label for="signature" class="block font-medium mb-1">
                    Upload Signature (PNG only)
                </label>

                <input type="file" name="signature" id="signature" accept="image/png" class="block w-full text-sm text-gray-700
                        file:mr-4 file:py-2 file:px-4
                        file:rounded file:border-0
                        file:text-sm file:font-semibold
                        file:bg-gray-100 file:text-gray-700
                        hover:file:bg-gray-200"
                    required />
                    

                <div class="text-center mt-6 mb-4">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layout>