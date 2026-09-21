<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — OnePage</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/png" href="{{ asset('onepage-blue.png') }}">

    <style>
        .jakarta { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="jakarta bg-white text-[#0B1020] antialiased">

    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- LEFT: FORM --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-10 lg:px-16 py-10 lg:py-0">
            <div class="w-full max-w-md mx-auto">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-[14px] font-medium text-[#6B7280] hover:text-[#0B1020] duration-200 mb-10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>
                    Back to Home
                </a>

                <a href="{{ route('welcome') }}" class="flex justify-center mb-8">
                    <img src="{{ asset('onepage-name.png') }}" alt="OnePage" class="h-[26px] w-auto">
                </a>

                <h1 class="m-0 mb-3 font-extrabold text-[32px] md:text-[38px] leading-[1.1] tracking-[-0.03em] text-[#0B1020]">Welcome back</h1>
                <p class="m-0 mb-9 text-[15px] leading-[1.65] text-[#5B6478]">Sign in to your OnePage account to pick up right where you left off.</p>

                @if (session()->has('error'))
                    <div class="rounded-2xl bg-red-50 border border-red-200 text-red-700 text-[13.5px] px-4 py-3 mb-6">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="rounded-2xl bg-red-50 border border-red-200 text-red-700 text-[13.5px] px-4 py-3 mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/login" class="flex flex-col gap-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-[13px] font-semibold text-[#0B1020] mb-2">Email Address</label>
                        <div class="flex items-center gap-3 rounded-2xl border border-[#E7EAF0] bg-[#F5F7FA] px-4 h-[52px] focus-within:border-[#1F6FEB] focus-within:bg-white duration-150">
                            <svg class="w-[18px] h-[18px] text-[#8B93A7] flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6.75Z" /><path d="m3 7 9 6 9-6" /></svg>
                            <input type="email" id="email" name="email" required autofocus placeholder="you@company.com"
                                class="w-full h-full text-[14.5px] outline-none bg-transparent border-none !rounded-none !p-0 placeholder:text-[#8B93A7]">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-[13px] font-semibold text-[#0B1020]">Password</label>
                            <a href="{{ route('password.request') }}" class="text-[13px] font-semibold text-[#1F6FEB] hover:text-[#0B1020] duration-200">Forgot password?</a>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-[#E7EAF0] bg-[#F5F7FA] px-4 h-[52px] focus-within:border-[#1F6FEB] focus-within:bg-white duration-150">
                            <svg class="w-[18px] h-[18px] text-[#8B93A7] flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                            <input type="password" id="password" name="password" required placeholder="••••••••"
                                class="w-full h-full text-[14.5px] outline-none bg-transparent border-none !rounded-none !p-0 placeholder:text-[#8B93A7]">
                            <button type="button" id="togglePassword" class="flex-none text-[#8B93A7] hover:text-[#0B1020] duration-150" aria-label="Toggle password visibility">
                                <svg id="eyeIcon" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="mt-3 inline-flex items-center justify-center bg-[#0B1020] text-white text-[15px] font-semibold px-[30px] py-4 rounded-full hover:bg-[#1F6FEB] duration-200">
                        Sign In
                    </button>
                </form>

                <p class="mt-8 text-[13.5px] text-[#8B93A7]">
                    New to OnePage? <a href="{{ route('demo.index') }}" class="font-semibold text-[#1F6FEB] hover:text-[#0B1020] duration-200">Book a demo</a> to get started.
                </p>
            </div>
        </div>

        {{-- RIGHT: MARKETING PANEL --}}
        <div class="hidden lg:flex w-full lg:w-1/2 bg-[#141A33] px-10 xl:px-16 py-16 items-center overflow-hidden relative">
            <div class="max-w-md mx-auto w-full relative z-10">
                <span class="inline-flex items-center bg-white/10 text-white text-xs font-semibold px-[15px] py-[7px] rounded-full mb-7">New to OnePage?</span>
                <h2 class="m-0 mb-4 font-extrabold text-[32px] xl:text-[36px] leading-[1.14] tracking-[-0.03em] text-white">One clear process, <span class="text-[#3DE0C8]">from draft to approval.</span></h2>
                <p class="m-0 mb-10 max-w-[42ch] text-[15px] leading-[1.65] text-white/68">OnePage turns document handling, approvals and corrective actions into one place your whole team can trust.</p>

                <div class="flex flex-col gap-4">
                    <div class="bg-white rounded-2xl px-[18px] py-4 shadow-[0_16px_38px_rgba(0,0,0,.3)] w-[200px]">
                        <div class="text-[12.5px] font-bold mb-2.5 text-[#0B1020]">Pending reviews</div>
                        <div class="text-[28px] font-extrabold tracking-[-0.03em] text-[#1F6FEB]">0</div>
                    </div>
                    <div class="bg-white rounded-2xl px-[18px] py-4 shadow-[0_16px_38px_rgba(0,0,0,.3)] w-[214px] ml-10">
                        <div class="text-[12.5px] font-bold mb-2.5 text-[#0B1020]">Pending approvals</div>
                        <div class="text-[28px] font-extrabold tracking-[-0.03em] text-[#1F6FEB]">2</div>
                    </div>
                    <div class="bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] rounded-2xl px-[18px] py-3.5 text-white shadow-[0_16px_38px_rgba(0,0,0,.3)] w-[180px] ml-4">
                        <div class="text-[11.5px] opacity-85 mb-1">Ave. Document Cycle</div>
                        <div class="text-[22px] font-extrabold tracking-[-0.03em]">21.4 hrs</div>
                    </div>
                </div>
            </div>

            <div class="absolute -top-24 -right-24 w-[420px] h-[420px] rounded-full bg-[#1F6FEB]/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-16 w-[380px] h-[380px] rounded-full bg-[#10C9B6]/10 blur-3xl"></div>
        </div>
    </div>

    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
        }
    });
    </script>
</body>
</html>
