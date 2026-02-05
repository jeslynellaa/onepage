<x-layout>
    <style>
        .img-frame {
            width: 100%;
            height: 100%;      /* square frame */
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
    </style>
    <div class="flex justify-center flex-col sm:flex-row bg-gray-100 h-250 sm:h-screen">
        <div class="w-full h-1/2 sm:w-9/16 sm:h-screen flex flex-col justify-center py-10">
            <div class="justify-center mx-auto w-4/6 min-w-16 flex gap-3 flex-col">
                <a href="" class="text-[#0047AB] hover:text-blue-500 pl-1 duration-300 w-36">
                    <i class="fa-solid fa-arrow-left mr-3"></i>Back to Home
                </a>
                <form method="POST" action="/login" class="bg-white p-6 rounded-3xl shadow-xl">
                    @csrf
                    <div class="mb-5">
                        <div class="rounded-lg bg-blue-300 w-12 h-12 justify-center mx-auto shadow-md mb-1">
                            <img src="{{ asset('onepage-blue.png') }}" alt="OnePage Logo">
                        </div>
                        <h2 class="text-2xl font-semibold text-center">Welcome back</h2>
                        <div class="text-center font-extralight">Sign in to your OnePage account</div>
                    </div>
                    <div class="flex flex-col gap-1 mb-3">
                        @error('email')
                            <div class="text-red-600 text-sm mb-2">{{ $message }}</div>
                        @enderror
                        <label for="email" class="text-xs pl-1">Email Address</label>
                        <div class="flex flex-row h-8">
                            <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-400 border-t border-l border-b w-9">
                                <i class="fa-regular fa-envelope text-gray-600"></i>
                            </div>
                            <input type="email" name="email" class="h-8 w-full border !border-gray-400 !border-l-0 !rounded-none !rounded-tr-lg !rounded-br-lg px-3 py-2 mb-3 outline-none focus:ring-0 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="password" class="text-xs pl-1">Password</label>
                        <div class="flex flex-row h-8">
                            <div class="flex items-center justify-center rounded-tl-lg rounded-bl-lg border-gray-400 border-t border-l border-b w-9">
                                <i class="fa-solid fa-lock text-gray-600"></i>
                            </div>
                            <input type="password" name="password" class="h-8 w-full border !border-gray-400 !border-l-0 !border-r-0 !rounded-none px-3 py-2 mb-3 outline-none focus:ring-0 focus:border-blue-500" required>
                            <button type="button" id="togglePassword" class="border border-l-0 border-gray-400 rounded-r-lg px-2 flex items-center justify-center cursor-pointer">
                                <i id="eyeIcon" class="fa-regular fa-eye text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-sm text-center mt-12">
                        <button type="submit" class="w-full bg-[#0047AB] hover:bg-blue-500 text-white py-2 rounded-lg font-semibold cursor-pointer duration-300">Sign in</button>
                    {{-- <a href="/register" class="text-blue-500 hover:underline">Create an account</a> --}}
                    </div>
                </form>
            </div>
        </div>
        <div class="w-full h-1/2 sm:w-7/16 sm:h-screen bg-gradient-to-tl from-[#3de3b1] to-[#575df9] text-white p-12 flex justify-center">
            <div class="h-full flex flex-col justify-center">
                <div class="gap-2 flex justify-center items-center rounded-lg w-40 py-1 px-3 mb-3 bg-white/75 text-[#0047AB]">
                    <i class="fa-regular fa-circle-question"></i>
                    <span class="text-sm">New to OnePage?</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Transform Your ISO Compliance Today</h3>
                <p class="text-sm">
                    OnePage takes the complexity out of ISO compliance - streamline your documents, stay organized, and move toward certification with confidence.
                </p>
                <div class="flex flex-col gap-3 mt-3 pl-2 text-sm mb-3">
                    <div>
                        <div class="flex items-baseline gap-2">
                            <i class="fa-solid fa-star"></i>
                            <span>Eliminate manual document tracking</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <i class="fa-solid fa-star"></i>
                            <span>Automate corrective action workflows</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <i class="fa-solid fa-star"></i>
                            <span>Real-time approval process monitoring</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <i class="fa-solid fa-star"></i>
                            <span>Centralized hub for your documents</span>
                        </div>
                    </div>
                </div>
                <div class="self-center h-50 w-90 border bg-gradient-to-br from-white/10 to-white/40 rounded-2xl p-3">
                    <div class="border rounded-xl h-full img-frame justify-center mx-auto">
                        <img src="{{ asset('/img/login-page-img.png') }}" alt="sample-image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>