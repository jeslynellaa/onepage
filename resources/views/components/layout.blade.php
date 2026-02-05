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

  <body x-data="{ collapsed: true, mobileOpen: false }" class="flex bg-gray-100">
    @auth
    <!-- Sidebar -->
    <aside 
      :class="collapsed ? 'w-16 rounded-tr-3xl rounded-br-3xl' : 'w-56'" 
      class="fixed top-0 left-0 h-screen bg-gradient-to-tr from-[#3de3b1] to-[#575df9] text-white transition-all duration-300 shadow-md flex flex-col z-20 overflow-y-auto">
      <!-- Sidebar Header -->
      <div class="flex mx-auto w-10/12 items-center justify-between px-4 py-3 border-b border-gray-300 text-center h-14">
        <h2 x-show="!collapsed" class="text-xl font-semibold text-white">OnePage</h2>
        <button 
          @click="collapsed = !collapsed" 
          class="text-white cursor-pointer transition-colors duration-200 hover:text-gray-200"
          :class="collapsed ? 'justify-center w-full py-1' : ''"
        >
          <i class="fa-solid fa-bars text-lg"></i>
        </button>
      </div>

      <!-- Nav Links -->
      <nav class="mt-4 flex flex-col items-center flex-1 space-y-1 w-11/12 mx-auto">

        <!-- DASHBOARD -->
        <a href="{{ route('dashboard') }}" x-data="{ showTooltip: false }"
          @mouseenter="showTooltip = true" 
          @mouseleave="showTooltip = false" 
          class="relative flex h-10 items-center px-4 py-2 rounded-2xl transition-colors duration-300 
            {{ request()->routeIs('dashboard')
            ? 'bg-gradient-to-br from-white/15 to-white/60 text-[#001f3f]'
            : 'text-white hover:text-white hover:border hover:border-white'
          }}"
            :class="collapsed ? 'justify-center w-10' : 'w-full'"
        >
          <i class="fa-solid fa-house text-lg"></i>
          <span x-show="!collapsed" class="ml-3">Dashboard</span>
          <div x-show="collapsed && showTooltip" class="fixed left-13 ml-3 px-2 py-1 text-sm bg-gray-800 text-white rounded shadow-lg whitespace-nowrap z-[9999]">Dashboard</div>
        </a>

        <!-- DOCUMENTS -->
        <a href="{{ route('document.index') }}" x-data="{ showTooltip: false }"
          @mouseenter="showTooltip = true" 
          @mouseleave="showTooltip = false"
          class="relative flex h-10 items-center px-4 py-2 rounded-2xl transition-colors duration-300 
            {{ request()->routeIs('document.*') 
              ? 'bg-gradient-to-br from-white/15 to-white/60 text-[#001f3f]' 
              : 'text-white hover:text-white hover:border hover:border-white' 
          }}"
            :class="collapsed ? 'justify-center w-10' : 'w-full'"
        >
          <i class="fa-solid fa-file-lines text-lg"></i>
          <span x-show="!collapsed" class="ml-3">Documents</span>
          <div x-show="collapsed && showTooltip" class="fixed left-13 ml-3 px-2 py-1 text-sm bg-gray-800 text-white rounded shadow-lg whitespace-nowrap z-[9999]">Documents</div>
        </a>

        <!-- SETTINGS -->
        <a href="#" x-data="{ showTooltip: false }"
          @mouseenter="showTooltip = true" 
          @mouseleave="showTooltip = false" 
          class="relative flex h-10 items-center px-4 py-2 rounded-2xl transition-colors duration-300 
            {{ request()->routeIs('settings') 
              ? 'bg-gradient-to-br from-white/15 to-white/60 text-[#001f3f]' 
              : 'text-white hover:text-white hover:border hover:border-white' 
          }}"
            :class="collapsed ? 'justify-center w-10' : 'w-full'"
        >
          <i class="fa-solid fa-gear text-lg"></i>
          <span x-show="!collapsed" class="ml-3">Settings</span>
          <div x-show="collapsed && showTooltip" class="fixed left-13 ml-3 px-2 py-1 text-sm bg-gray-800 text-white rounded shadow-lg whitespace-nowrap z-[9999]">Settings</div>
        </a>

        <!-- ADMIN -->
        <a href="{{ route('admin.index') }}" x-data="{ showTooltip: false }"
          @mouseenter="showTooltip = true" 
          @mouseleave="showTooltip = false" 
          class="relative flex h-10 items-center px-4 py-2 rounded-2xl transition-colors duration-300 
            {{ request()->routeIs('admin.*') 
              ? 'bg-gradient-to-br from-white/15 to-white/60 text-[#001f3f]' 
              : 'text-white hover:text-white hover:border hover:border-white' 
          }}"
            :class="collapsed ? 'justify-center w-10' : 'w-full'"
        >
          <i class="fa-solid fa-user-tie"></i>
          <span x-show="!collapsed" class="ml-3">Admin</span>
          <div x-show="collapsed && showTooltip" class="fixed left-13 ml-3 px-2 py-1 text-sm bg-gray-800 text-white rounded shadow-lg whitespace-nowrap z-[9999]">Admin</div>
        </a>

      </nav>
    </aside>

    <!-- Header -->
    <header 
      class="fixed top-0 left-0 bg-gray-100 z-10 h-14 flex items-center justify-end transition-all duration-300 w-full"
      :class="collapsed ? 'pl-16' : 'pl-56'"
    >
      <div class="hidden lg:flex flex-col justify-center px-5 h-full w-3/4">
        @php
          $hour = now()->format('H');
          if ($hour < 12) {
            $greeting = 'Good Morning';
          } elseif ($hour < 18) {
            $greeting = 'Good Afternoon';
          } else {
            $greeting = 'Good Evening';
          }
        @endphp

        <h4 class="text-gray font-semibold text-lg leading-tight m-0">
          @auth
            {{ $greeting }}, {{ auth()->user()->first_name }}! (<span class="uppercase">{{ auth()->user()->company->name }}</span>)
          @else
            {{ $greeting }}!
          @endauth
        </h4>
        <div class="text-sm leading-none mt-0"> {{ date('M. d, Y - l h:ia') }} </div>
      </div>
      @auth
      <div class="flex justify-end items-center px-5 h-full w-1/2 gap-2">
        <a href="{{route('profile.edit', auth()->user()->id)}}" class="text-lg cursor-pointer transition-colors duration-200 text-gray-600 hover:underline"><i class="fa-solid fa-user" title="Profile"></i></a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="text-lg cursor-pointer transition-colors duration-200 text-gray-600 hover:underline" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
          </button>
        </form>
      </div>
      @endauth
    </header>
    @endauth

    <!-- Main content area -->
    <main class="flex-1 transition-all duration-300 {{ request()->routeIs('login', 'register') ? 'ml-0' : '' }}" :class="!{{ request()->routeIs('login', 'register') ? 'true' : 'false' }} ? (collapsed ? 'ml-16' : 'ml-56') : ''">


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
        @auth
        @if(!(request()->routeIs('login') || request()->routeIs('register')))
          <script>
            setInterval(() => {
                fetch('/auth-check', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        if (res.status === 401) {
                            window.location.href = '/login';
                        }
                    });
            }, 60000);

            window.addEventListener('pageshow', event => {
              if (event.persisted) {
                  window.location.href = '/login';
              }
            });
          </script>
        @endif
        @endauth
      </section>

      <!-- Footer -->
      <footer class="border-t text-center text-sm text-gray-500 py-3">
        <p class="m-0">
          &copy; {{ date('Y') }}
          <a href="/" class="text-gray-500 hover:text-sky-600 transition-colors">OnePage</a>.
          All rights reserved.
        </p>
      </footer>
    </main>
  </body>
</html>