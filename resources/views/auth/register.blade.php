<x-layout>
    <div class="h-130 w-150 bg-blue-300 absolute rounded-full -top-70 -left-40 blur-3xl"></div>
    <div class="h-170 w-170 bg-green-200 absolute rounded-full -bottom-70 -right-50 blur-3xl"></div>
    <div class="flex flex-col lg:flex-row justify-center h-screen lg:h-140 lg:gap-6 items-center relative px-10 lg:px-20 -top-10 lg:top-0">
        <div class="p-6 w-full lg:w-1/2 lg:h-1/2 justify-center items-center flex flex-col">
            <img src="{{asset('/onepage-name.png')}}" alt="OnePage Logo" class="w-50 lg:w-full">
            <div class="lg:text-lg font-semibold text-gray-600 text-right lg:w-full ">
                <span class="border border-blue-500 px-5 rounded-full bg-green-100/50">by FCU Solutions Inc.</span>
            </div>
            <h1 class="text-xl lg:text-[3rem] font-bold lg:text-left text-center lg:leading-none">The better way to handle documents</h1>
        </div>
        <div class="w-full lg:w-1/2 flex justify-center">
            <form method="POST" action="/register" class="lg:w-3/4 bg-white p-6 rounded-3xl shadow-md">
                @csrf
                <h2 class="text-2xl font-semibold text-center">Register</h2>
                <div class="w-full text-center">You have been invited to register an account under</div>
                <div class="font-semibold w-full text-center">{{$company->name}}</div>
                <input type="hidden" name="token" value="{{ request('token') }}">
                <input type="hidden" name="role" value="{{ $invitation->role }}">
                <input type="hidden" name="company_id" value="{{ $invitation->company_id }}">

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <input type="text" name="first_name" placeholder="Given Name" class="col-span-2 rounded px-3" required>

                    <input type="text" name="middle_name" placeholder="Middle Name" class="px-2 border rounded" required>

                    <input type="text" name="last_name" placeholder="Last Name" class="px-2 border rounded" required>

                    <input type="text" name="username" placeholder="User Name" class="col-span-2 px-2 border rounded" required>

                    <input type="email" name="email" placeholder="Email" value="{{ $invitation->email }}" class="col-span-2 border rounded px-3 py-2" required>
                    
                    <div class="flex flex-row h-10 col-span-2">
                        <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-300 border-t border-l border-b w-9">
                            <i class="fa-solid fa-lock text-gray-600"></i>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Password" class="h-10 w-full border !border-gray-300 !border-l-0 !border-r-0 !rounded-none px-3 py-2 outline-none focus:ring-0 focus:border-blue-500" required>
                        <button type="button" class="toggle-password border border-l-0 border-gray-300 rounded-r-lg px-2 flex items-center justify-center cursor-pointer">
                            <i class="fa-regular fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    
                    <div class="flex flex-row h-10 col-span-2">
                        <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-300 border-t border-l border-b w-9">
                            <i class="fa-solid fa-lock text-gray-600"></i>
                        </div>
                        <input type="password" id="password" name="password_confirmation" placeholder="Confirm Password" class="h-10 w-full border !border-gray-300 !border-l-0 !border-r-0 !rounded-none px-3 py-2 outline-none focus:ring-0 focus:border-blue-500" required>
                        <button type="button" class="toggle-password border border-l-0 border-gray-300 rounded-r-lg px-2 flex items-center justify-center cursor-pointer">
                            <i class="fa-regular fa-eye text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 mt-4 rounded">
                    Register
                </button>
                <hr class="mt-3 border-gray-400">
                <div class="text-sm text-center mt-3 flex justify-center gap-1">
                    <span class="text-gray-700">Already have an account?</span>
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Sign in</a>
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