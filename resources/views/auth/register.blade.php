<x-layout>
    <div class="h-130 w-150 bg-blue-300 absolute rounded-full -top-70 -left-40 blur-3xl"></div>
    <div class="h-170 w-170 bg-green-200 absolute rounded-full -bottom-70 -right-50 blur-3xl"></div>
    <div class="flex flex-col lg:flex-row justify-center lg:gap-6 items-center relative p-10 lg:px-20 mb-5">
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

                    <input type="text" name="middle_name" placeholder="Middle Name" class="px-2 border rounded">

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
                <div x-data="{ open: false }">
                    <div class="mt-4">
                        <label class="flex items-start text-sm text-gray-600">
                            <input type="checkbox" name="privacy_consent" required class="!w-16 mt-1 rounded border-gray-300 text-blue-600 !ring-transparent !focus:ring-transparent">
                            <span>I have read and agree to the <button type="button" @click="open = true" class="text-blue-600 hover:underline">Privacy Policy</button> and consent to the collection and processing of my personal data for account creation and platform use.</span>
                        </label>
                    </div>
                    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50 z-40" @click="open = false"></div>
                    {{-- PRIVACY STATEMENT MODAL --}}
                    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div @click.stop class="bg-white w-full max-w-2xl rounded-2xl shadow-xl flex flex-col max-h-[85vh]">
                                <div class="flex items-center justify-between px-6 py-4 border-b">
                                    <h3 class="text-lg font-semibold text-gray-800">Privacy Policy</h3>
                                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                                </div>
                                <div class="px-6 py-4 overflow-y-auto text-sm text-gray-700 space-y-4">
                                    <p>OnePage (“we”, “us”, or “our”) respects your right to data privacy and is committed to protecting your personal data in accordance with the Philippine Data Privacy Act of 2012 (RA 10173).</p>
                                    <p><strong>Information We Collect</strong><br>We collect personal information such as your name, email address, company details, and login credentials when you register and use the platform.</p>
                                    <p><strong>Purpose of Collection</strong><br>Your personal data is processed solely for account creation, authentication, access to platform features, and system-related communications.</p>
                                    <p><strong>Data Sharing</strong><br>Your data is accessible only to authorized personnel and trusted service providers necessary to operate the platform.</p>
                                    <p><strong>Data Retention</strong><br>We retain personal data only for as long as necessary to fulfill its purpose or as required by law.</p>
                                    <p><strong>Data Protection</strong><br>We implement reasonable organizational, physical, and technical security measures to protect your data.</p>
                                    <p><strong>Your Rights</strong><br>You have the right to access, correct, withdraw consent, and request deletion of your personal data, subject to applicable laws.</p>
                                    <p>If you have questions, contact us at <strong>onepagefcu@gmail.com</strong>.</p>
                                </div>
                                <div class="px-6 py-4 border-t flex justify-end">
                                    <button type="button" @click="open = false" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Close</button>
                                </div>
                            </div>
                        </div>
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