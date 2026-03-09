<x-layout>
    <style>
        .input-group{
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .input-container{
            display: flex;
            flex-direction: row;
            height: 2.5rem;
        }
        .input-label{
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 0.25rem;
            padding: 0.5rem;
            border-top-left-radius: 1rem;
            border-bottom-left-radius: 1rem;
            --tw-border-opacity: 1;
            border-color: rgba(156, 163, 175, var(--tw-border-opacity));
            border-top-width: 1px;
            border-left-width: 1px;
            border-bottom-width: 1px;
            width: 13rem;
            text-align: left;
            color: gray;
        }
        .input-append{
            height: 2.5rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.75rem;
            border-width: 1px;
            --tw-border-opacity: 1;
            border-color: rgba(156, 163, 175, var(--tw-border-opacity)) !important;
            border-left-width: 0px !important;
            border-radius: 0px !important;
            border-top-right-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
            outline: 1px solid transparent;
            outline-offset: 1px;
        }
        .input-password{
            height: 2.5rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.75rem;
            border-width: 1px;
            --tw-border-opacity: 1;
            border-color: rgba(156, 163, 175, var(--tw-border-opacity)) !important;
            border-left-width: 0px !important;
            border-right-width: 0px !important;
            border-radius: 0px !important;
            outline: 1px solid transparent;
            outline-offset: 1px;
        }
        .input-end-btn{
            height: 2.5rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.75rem;
            border-width: 1px;
            --tw-border-opacity: 1;
            border-color: rgba(156, 163, 175, var(--tw-border-opacity)) !important;
            border-left-width: 0px !important;
            border-radius: 0px !important;
            border-top-right-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
            outline: 1px solid transparent;
            outline-offset: 1px;
        }
    </style>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            Edit User Profile
        </h1>

        {{-- Edit Profile --}}
        <div class="shadow-md rounded-2xl md:w-3/4 lg:w-1/2 bg-white p-5 mt-2 mx-auto mb-4">
            <form method="POST" action="{{ route('profile.update', $user->id) }}"  enctype="multipart/form-data" class="space-y-6" >
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-3">
                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <div class="w-full whitespace-nowrap">Given Name <span class="text-red-500">*</span></div>
                            </div>
                            <input id="first_name" type="text" name="first_name" value="{{$user->first_name ?? old('first_name')}}" class="input-append focus:ring-0 focus:border-blue-500 @error('first_name') is-invalid @enderror" required/>
                        </div>
                        @error('first_name')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <div class="w-full whitespace-nowrap">Middle Name</div>
                            </div>
                            <input id="middle_name" type="text" name="middle_name" value="{{$user->middle_name ?? old('middle_name')}}" class="input-append focus:ring-0 focus:border-blue-500 @error('middle_name') is-invalid @enderror"/>
                        </div>
                        @error('middle_name')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <div class="w-full whitespace-nowrap">Last Name <span class="text-red-500">*</span></div>
                            </div>
                            <input id="last_name" type="text" name="last_name" value="{{$user->last_name ?? old('last_name')}}" class="input-append focus:ring-0 focus:border-blue-500 @error('last_name') is-invalid @enderror" required/>
                        </div>
                        @error('last_name')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <i class="fa-solid fa-address-card text-gray"></i>
                                <div class="w-full whitespace-nowrap">User Name <span class="text-red-500">*</span></div>
                            </div>
                            <input id="username" type="text" name="username" value="{{$user->username ?? old('username')}}" class="input-append focus:ring-0 focus:border-blue-500 @error('username') is-invalid @enderror" required/>
                        </div>
                        @error('username')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <div class="input-container">
                            <div class="input-label">
                                <i class="fa-regular fa-envelope text-gray"></i> 
                                <div class="w-full whitespace-nowrap">Email Address <span class="text-red-500">*</span></div>
                            </div>
                            <input id="email" type="email" name="email" value="{{$user->email ?? old('email')}}" class="input-append focus:ring-0 focus:border-blue-500 @error('email') is-invalid @enderror" required/>
                        </div>
                        @error('email')
                            <span class="text-xs text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <label for="signature" class="block font-medium mb-1 text-xs uppercase">
                    <i class="fa-solid fa-signature mr-1"></i>Upload Signature (PNG only)
                </label>
                <img src="{{ asset('storage/'.$user->signature_path)}}" alt="signature" class="mx-auto w-80 rounded">

                <input type="file" name="signature" id="signature" accept="image/png" class="w-full text-sm text-gray-700 !rounded-2xl border-gray-700 mb-0
                        file:mr-4 file:py-2 file:px-4
                        file:rounded file:border-0
                        file:text-sm file:font-semibold
                        file:bg-gray-100 file:text-gray-700
                        hover:file:bg-gray-200" />
                <span class="text-xs italic text-gray-500 m-0 p-0">* A transparent PNG image provides the best results when generating signed documents.</span>
                    

                <div class="text-center mt-6 mb-4">
                    <button type="submit" class="px-6 py-2 cursor-pointer bg-green-600 text-white rounded-3xl hover:bg-green-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password Form --}}
        <div class="shadow-md rounded-2xl md:w-3/4 lg:w-1/2 bg-white p-5 mt-2 mx-auto">
            <form action="{{ route('profile.password.update', $user->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="input-group">
                    <div class="input-container">
                        <div class="input-label">
                            <i class="fa-solid fa-lock text-gray"></i>
                            <div class="w-[30rem] whitespace-nowrap">Current Password <span class="text-red-500">*</span></div>
                        </div>
                        <input id="current_password" type="password" name="current_password" class="input-password focus:ring-0 focus:border-blue-500 @error('current_password') is-invalid @enderror" required/>
                        <button type="button" class="toggle-password input-end-btn cursor-pointer">
                            <i id="eyeIcon" class="fa-regular fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <span class="text-xs text-red-600" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div><div class="input-group">
                    <div class="input-container">
                        <div class="input-label">
                            <i class="fa-solid fa-lock text-gray"></i>
                            <div class="w-[30rem] whitespace-nowrap">New Password <span class="text-red-500">*</span></div>
                        </div>
                        <input id="password" type="password" name="password" class="input-password focus:ring-0 focus:border-blue-500 @error('password') is-invalid @enderror" required/>
                        <button type="button" class="toggle-password input-end-btn cursor-pointer">
                            <i id="eyeIcon" class="fa-regular fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-xs text-red-600" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-group">
                    <div class="input-container">
                        <div class="input-label">
                            <i class="fa-solid fa-lock text-gray"></i>
                            <div class="w-[30rem] whitespace-nowrap">Confirm New Password <span class="text-red-500">*</span></div>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="input-password focus:ring-0 focus:border-blue-500" required/>
                        <button type="button" class="toggle-password input-end-btn cursor-pointer">
                            <i id="eyeIcon" class="fa-regular fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="text-xs text-red-600" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="text-center mt-6">
                    <button type="submit" class="px-6 py-2 cursor-pointer bg-blue-600 text-white rounded-3xl hover:bg-blue-700 transition">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.previousElementSibling;
                const icon = button.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</x-layout>