<x-layout>
    <style>
        .img-frame {
            width: 100%;
            height: 100%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
    </style>

    <div class="flex justify-center flex-col lg:flex-row bg-gray-100 h-250 lg:h-screen">

        <!-- Left Side -->
        <div class="w-full h-1/2 lg:w-9/16 lg:h-screen flex flex-col justify-center py-10">
            <div class="justify-center mx-auto w-3/4 min-w-min max-w-md flex gap-3 flex-col">

                <a href="{{ route('login') }}" class="text-[#0047AB] hover:text-blue-500 pl-1 duration-300 w-40">
                    <i class="fa-solid fa-arrow-left mr-3"></i>Back to Sign In
                </a>

                <form method="POST" action="{{ route('password.update') }}" class="bg-white p-6 rounded-3xl shadow-xl">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-5">
                        <div class="rounded-lg bg-blue-300 w-12 h-12 justify-center mx-auto shadow-md mb-1">
                            <img src="{{ asset('onepage-blue.png') }}" alt="OnePage Logo">
                        </div>

                        <h2 class="text-2xl font-semibold text-center">
                            Reset Password
                        </h2>

                        <p class="text-center font-extralight text-sm mt-2">
                            Create a new password for your OnePage account.
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 rounded-lg bg-green-100 text-green-700 p-3 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-100 text-red-700 p-3 text-sm">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Email -->
                    <div class="flex flex-col gap-1 mb-3">
                        <label class="text-xs pl-1">Email Address</label>

                        <div class="flex flex-row h-8">
                            <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-400 border-t border-l border-b w-9">
                                <i class="fa-regular fa-envelope text-gray-600"></i>
                            </div>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $email) }}"
                                readonly
                                class="h-8 w-full border !border-gray-400 !border-l-0 !rounded-none !rounded-tr-lg !rounded-br-lg px-3 py-2 outline-none bg-gray-100">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col gap-1 mb-3">
                        <label class="text-xs pl-1">New Password</label>

                        <div class="flex flex-row h-8">
                            <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-400 border-t border-l border-b w-9">
                                <i class="fa-solid fa-lock text-gray-600"></i>
                            </div>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="h-8 w-full border !border-gray-400 !border-l-0 !border-r-0 !rounded-none px-3 py-2 outline-none focus:ring-0 focus:border-blue-500">

                            <button type="button" id="togglePassword" class="border border-l-0 border-gray-400 rounded-r-lg px-2 flex items-center justify-center cursor-pointer">
                                <i id="eyeIconPassword" class="fa-regular fa-eye text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="flex flex-col gap-1 mb-6">
                        <label class="text-xs pl-1">Confirm Password</label>

                        <div class="flex flex-row h-8">
                            <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-400 border-t border-l border-b w-9">
                                <i class="fa-solid fa-lock text-gray-600"></i>
                            </div>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                class="h-8 w-full border !border-gray-400 !border-l-0 !border-r-0 !rounded-none px-3 py-2 outline-none focus:ring-0 focus:border-blue-500">

                            <button type="button" id="toggleConfirmPassword" class="border border-l-0 border-gray-400 rounded-r-lg px-2 flex items-center justify-center cursor-pointer">
                                <i id="eyeIconConfirm" class="fa-regular fa-eye text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-[#0047AB] hover:bg-blue-500 text-white py-2 rounded-lg font-semibold cursor-pointer duration-300">
                        Reset Password
                    </button>
                </form>

            </div>
        </div>

        <!-- Right Side -->
        <div class="w-full h-1/2 lg:w-7/16 lg:h-screen bg-gradient-to-tl from-[#3de3b1] to-[#575df9] text-white p-12 flex justify-center">
            <div class="h-full flex flex-col justify-center">

                <div class="gap-2 flex justify-center items-center rounded-lg w-52 py-1 px-3 mb-3 bg-white/75 text-[#0047AB]">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span class="text-sm">Secure Password Reset</span>
                </div>

                <h3 class="text-xl font-semibold mb-3">
                    Protect Your OnePage Account
                </h3>

                <p class="text-sm">
                    Choose a strong password to keep your documents, workflows, and ISO compliance data secure.
                </p>

                <div class="flex flex-col gap-3 mt-3 pl-2 text-sm mb-3">
                    <div class="flex items-baseline gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Encrypted password storage</span>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Secure password reset links</span>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Industry-standard authentication</span>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Designed for ISO management teams</span>
                    </div>
                </div>

                <div class="self-center h-50 w-90 border bg-gradient-to-br from-white/10 to-white/40 rounded-2xl p-3">
                    <div class="border rounded-xl h-full img-frame">
                        <img src="{{ asset('/img/login-page-img.PNG') }}" alt="sample-image">
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        function setupPasswordToggle(buttonId, inputId, iconId) {
            document.getElementById(buttonId).addEventListener('click', function () {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

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
        }

        setupPasswordToggle('togglePassword', 'password', 'eyeIconPassword');
        setupPasswordToggle('toggleConfirmPassword', 'password_confirmation', 'eyeIconConfirm');
    </script>

</x-layout>
