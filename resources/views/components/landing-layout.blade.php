<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>OnePage</title>

    {{-- Vite compiled CSS & JS (Tailwind, Alpine, etc.) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/cd50bed0a1.js" crossorigin="anonymous"></script>
    <!-- icon -->
    <link rel="icon" type="image/png" href="{{ asset('onepage-blue.png') }}">
    {{-- Datatables link --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />

    <style>
      .text-xs {
        font-size: 13px;
      }
      aside {
        overflow-x: visible !important;
      }
    </style>
  </head>

  <body class="flex bg-gray-100">
    <!-- Header -->
    <header x-data="{ open: false }" class="fixed top-0 left-0 bg-gray-100 z-10 h-16 flex items-center transition-all duration-300 w-full">
      <div class="flex flex-row justify-between lg:justify-start items-center px-20 h-full w-full py-1">

        <div class="h-full lg:w-1/6 text-center">
          <div class="h-full flex flex-col justify-center w-34 py-1">
            <div class="w-full h-3/4 mx-auto flex justify-center">
              <img class="h-full w-full object-contain" src="{{ asset('onepage-name.png') }}" alt="OnePage">
            </div>
            <div class="flex justify-center whitespace-nowrap font-medium text-[8pt]">
              by FCU SOLUTIONS INC.
            </div>
          </div>
        </div>
        <div class="hidden lg:w-3/6 h-full lg:block">
          <div class="flex flex-row justify-around items-center h-full">
            <a href="/#features-section" class="cursor-pointer duration-300 hover:text-[#0047ab]">Features</a>
            <a href="/#benefits-section" class="cursor-pointer duration-300 hover:text-[#0047ab]">Benefits</a>
            <a href="/#about-section" class="cursor-pointer duration-300 hover:text-[#0047ab]">About</a>
            <a href="/#contact-section" class="cursor-pointer duration-300 hover:text-[#0047ab]">Contact</a>
            <a href="" class="cursor-pointer duration-300 hover:text-[#0047ab]">Pricing</a>
            <a href="{{ route('faqs') }}" class="cursor-pointer duration-300 hover:text-[#0047ab]">FAQs</a>
          </div>
        </div>
        <div class="hidden lg:flex justify-end lg:w-2/6 h-full items-center gap-6">
          <a href="{{ route('login') }}" class="font-semibold hover:text-[#0047ab] duration-300">Sign In</a>

          <a href="" class="bg-gradient-to-tl from-[#3de3b1] to-[#575df9] text-white py-2 px-4 rounded-xl">Get Started</a>
        </div>

        {{-- mobile navigation links --}}
        <button @click="open = !open" class="lg:hidden p-2 rounded-lg hover:bg-gray-200 transition cursor-pointer duration-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div x-show="open" x-transition @click.outside="open = false" class="lg:hidden inline-block absolute top-16 left-0 w-full bg-white shadow-xl">
          <div class="flex flex-col divide-y">
            <a href="#features-section" class="px-6 py-4 hover:bg-gray-100">Features</a>
            <a href="#benefits-section" class="px-6 py-4 hover:bg-gray-100">Benefits</a>
            <a href="#about-section" class="px-6 py-4 hover:bg-gray-100">About</a>
            <a href="#contact-section" class="px-6 py-4 hover:bg-gray-100">Contact</a>
            <a href="" class="px-6 py-4 hover:bg-gray-100">Pricing</a>

            <div class="px-6 py-4 flex flex-col gap-3">
              <a href="{{ route('login') }}" class="font-semibold">Sign In</a>
              <a href="" class="bg-gradient-to-tl from-[#3de3b1] to-[#575df9] text-white py-2 px-4 rounded-xl text-center">
                  Get Started
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main content area -->
    <main class="flex-1 transition-all duration-300">
      <!-- Page Content -->
      <section class="{{!(request()->routeIs('login')) ? 'pt-14' : ''}} min-h-full flex-grow">
        <!-- Flash Messages -->
        @if (session()->has('success'))
          <div class="max-w-4xl mx-auto px-4">
            <div class="bg-green-100 border border-green-300 text-green-800 text-center px-4 py-3 rounded-2xl mb-2">
              {{ session('success') }}
            </div>
          </div>
        @endif

        @if (session()->has('error'))
          <div class="max-w-4xl mx-auto px-4">
            <div class="bg-red-100 border border-red-300 text-red-800 text-center px-4 py-3 rounded-2xl mb-2">
              {{ session('failure') }}
            </div>
          </div>
        @endif
        @if ($errors->any())
          <div class="max-w-4xl mx-auto px-4">
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-2xl mb-2">
              <ul class="list-disc list-inside text-left">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif

        {{ $slot }}

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
        {{ $scripts ?? '' }}
      </section>

      <!-- Footer -->
      <footer class="border-t border-gray-300 bg-blue-600 text-center text-sm text-gray-100 py-3">
        <p class="m-0">
          &copy; {{ date('Y') }}
          <a href="/" class="hover:text-sky-600 transition-colors">OnePage</a>.
          All rights reserved.
        </p>
      </footer>
    </main>
  </body>
</html>