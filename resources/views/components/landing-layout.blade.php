@props(['title' => 'OnePage'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('landingNav', (initial, spy) => ({
                active: initial,
                lockUntil: 0,
                sectionIds: ['features', 'benefits', 'about'],

                init() {
                    if (! spy) return;

                    const hash = window.location.hash.slice(1);
                    if (this.sectionIds.includes(hash)) this.active = hash;

                    const sections = this.sectionIds
                        .map((id) => document.getElementById(id))
                        .filter(Boolean);
                    if (! sections.length) return;

                    const sync = () => {
                        if (Date.now() < this.lockUntil) return;

                        const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2;
                        if (atBottom) {
                            this.active = sections[sections.length - 1].id;
                            return;
                        }

                        let current = 'home';
                        sections.forEach((el) => {
                            if (el.getBoundingClientRect().top <= 140) current = el.id;
                        });
                        this.active = current;
                    };

                    window.addEventListener('scroll', sync, { passive: true });
                    window.addEventListener('resize', sync, { passive: true });
                    sync();
                },

                // Highlight immediately on click, and hold it while the smooth scroll settles.
                go(id) {
                    this.active = id;
                    this.lockUntil = Date.now() + 800;
                },
            }));
        });
    </script>

    <link rel="icon" type="image/png" href="{{ asset('onepage-blue.png') }}">

    <style>
        html { scroll-behavior: smooth; }
        .jakarta { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        [x-cloak] { display: none !important; }

        @keyframes swing-a { 0%, 100% { transform: rotate(-8deg); } 50% { transform: rotate(8deg); } }
        @keyframes swing-b { 0%, 100% { transform: rotate(-6deg) translateY(0); } 50% { transform: rotate(6deg) translateY(-4px); } }
        @keyframes swing-c { 0%, 100% { transform: rotate(7deg); } 50% { transform: rotate(-7deg); } }
        .floaty { transform-origin: top center; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .floaty { animation: none !important; }
        }
    </style>
</head>
<body class="jakarta bg-white text-[#0B1020] antialiased" x-data="{ mobileOpen: false, videoOpen: false }">

    {{-- NAV --}}
    @php
        $onWelcome = request()->routeIs('welcome');
        $navActive = match (true) {
            request()->routeIs('pricing') => 'pricing',
            request()->routeIs('faqs') => 'faqs',
            $onWelcome => 'home',
            default => '',
        };
    @endphp

    <header x-data="landingNav('{{ $navActive }}', {{ $onWelcome ? 'true' : 'false' }})"
            class="fixed top-0 inset-x-0 z-50 bg-white border-b border-[#E7EAF0]">
        <div class="max-w-[1240px] mx-auto flex items-center gap-6 md:gap-9 px-5 md:px-10 py-[18px] md:py-[22px]">
            <a href="{{ route('welcome') }}" class="flex-none">
                <img src="{{ asset('onepage-name.png') }}" alt="OnePage" class="h-[22px] md:h-[26px] w-auto">
            </a>

            @php
                $navLinks = [
                    ['key' => 'home',     'label' => 'Home',     'href' => route('welcome')],
                    ['key' => 'features', 'label' => 'Features', 'href' => route('welcome') . '#features'],
                    ['key' => 'benefits', 'label' => 'Benefits', 'href' => route('welcome') . '#benefits'],
                    ['key' => 'about',    'label' => 'About',    'href' => route('welcome') . '#about'],
                    ['key' => 'pricing',  'label' => 'Pricing',  'href' => route('pricing')],
                    ['key' => 'faqs',     'label' => 'FAQs',     'href' => route('faqs')],
                ];
            @endphp

            <nav class="hidden lg:flex items-center gap-8 mx-auto text-[14.5px] font-medium">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       @click="go('{{ $link['key'] }}')"
                       :aria-current="active === '{{ $link['key'] }}' ? 'page' : false"
                       :class="active === '{{ $link['key'] }}' ? 'text-[#0B1020]' : 'text-[#6B7280] hover:text-[#0B1020]'"
                       class="relative py-1 duration-200 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#1F6FEB]">
                        {{ $link['label'] }}
                        <span x-cloak x-show="active === '{{ $link['key'] }}'"
                              class="absolute -bottom-[3px] inset-x-0 h-[2px] rounded-full bg-[#1F6FEB]"></span>
                    </a>
                @endforeach
            </nav>

            <div class="hidden lg:flex items-center gap-6 ml-auto">
                <a href="{{ route('login') }}" class="text-[14.5px] font-semibold text-[#0B1020] hover:text-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Log In</a>
                <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#0B1020] text-white text-[14px] font-semibold px-[22px] py-3 rounded-full hover:bg-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Get Started</a>
            </div>

            <button @click="mobileOpen = !mobileOpen" class="lg:hidden ml-auto p-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]" aria-label="Toggle menu" :aria-expanded="mobileOpen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div x-show="mobileOpen" x-transition @click.outside="mobileOpen = false" class="lg:hidden border-t border-[#E7EAF0]">
            <div class="flex flex-col divide-y divide-[#E7EAF0] px-5">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       @click="go('{{ $link['key'] }}'); mobileOpen = false"
                       :aria-current="active === '{{ $link['key'] }}' ? 'page' : false"
                       :class="active === '{{ $link['key'] }}' ? 'text-[#0B1020] font-semibold' : 'text-[#5B6478] font-medium'"
                       class="relative py-3.5 pl-3.5 text-[15px] duration-200">
                        <span x-cloak x-show="active === '{{ $link['key'] }}'"
                              class="absolute left-0 top-2.5 bottom-2.5 w-[3px] rounded-full bg-[#1F6FEB]"></span>
                        {{ $link['label'] }}
                    </a>
                @endforeach
                <div class="py-4 flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="font-semibold text-center py-2">Log In</a>
                    <a href="{{ route('demo.index') }}" class="bg-[#0B1020] text-white text-center rounded-full py-3.5 font-semibold">Get Started</a>
                </div>
            </div>
        </div>
    </header>

    <main class="pt-[59px] md:pt-[71px]">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="max-w-[1240px] mx-auto px-5 md:px-10 pt-16 lg:pt-[88px] pb-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.6fr_1fr_1fr_1fr_1fr] gap-8 lg:gap-10">
            <div>
                <img src="{{ asset('onepage-name.png') }}" alt="OnePage" class="h-6 w-auto mb-4">
                <p class="m-0 mb-5 max-w-[34ch] text-[13.5px] leading-[1.65] text-[#5B6478]">Document management that meets simplicity and efficiency. A product of FCU Solutions Inc.</p>
                <div class="flex gap-2.5">
                    <a href="https://www.fcusolutions.org/" target="_blank" rel="noopener" aria-label="FCU Solutions on LinkedIn" class="w-[34px] h-[34px] rounded-full bg-[#F0F2F6] text-[#5B6478] flex items-center justify-center hover:bg-[#E7EAF0] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.61 0 4.28 2.38 4.28 5.47v6.27ZM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13ZM7.12 20.45H3.55V9h3.57v11.45Z"/></svg>
                    </a>
                    <a href="https://www.fcusolutions.org/" target="_blank" rel="noopener" aria-label="FCU Solutions on Facebook" class="w-[34px] h-[34px] rounded-full bg-[#F0F2F6] text-[#5B6478] flex items-center justify-center hover:bg-[#E7EAF0] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.5l.5-3H13.5V8.5c0-.9.25-1.5 1.55-1.5H16.5V4.3c-.27-.04-1.2-.13-2.28-.13-2.26 0-3.8 1.38-3.8 3.9V10.5H8v3h2.42V21h3.08Z"/></svg>
                    </a>
                    <a href="https://www.fcusolutions.org/" target="_blank" rel="noopener" aria-label="FCU Solutions website" class="w-[34px] h-[34px] rounded-full bg-[#F0F2F6] text-[#5B6478] flex items-center justify-center hover:bg-[#E7EAF0] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" /><path d="M3.6 9h16.8M3.6 15h16.8M12 3a13.5 13.5 0 0 1 0 18M12 3a13.5 13.5 0 0 0 0 18" /></svg>
                    </a>
                </div>
            </div>
            <div>
                <div class="text-[13.5px] font-bold mb-3.5">Company</div>
                <div class="flex flex-col gap-2.5 text-[13.5px]">
                    <a href="{{ route('welcome') }}#about" class="text-[#5B6478] hover:text-[#0B1020] duration-200">About Us</a>
                    <a href="https://www.fcusolutions.org/" target="_blank" rel="noopener" class="text-[#5B6478] hover:text-[#0B1020] duration-200">FCU Solutions</a>
                    <a href="{{ route('demo.index') }}" class="text-[#5B6478] hover:text-[#0B1020] duration-200">Contact</a>
                </div>
            </div>
            <div>
                <div class="text-[13.5px] font-bold mb-3.5">Product</div>
                <div class="flex flex-col gap-2.5 text-[13.5px]">
                    <a href="{{ route('welcome') }}#features" class="text-[#5B6478] hover:text-[#0B1020] duration-200">Features</a>
                    <a href="{{ route('welcome') }}#benefits" class="text-[#5B6478] hover:text-[#0B1020] duration-200">Benefits</a>
                    <a href="{{ route('pricing') }}" class="text-[#5B6478] hover:text-[#0B1020] duration-200">Pricing</a>
                </div>
            </div>
            <div>
                <div class="text-[13.5px] font-bold mb-3.5">Resources</div>
                <div class="flex flex-col gap-2.5 text-[13.5px]">
                    <a href="{{ route('faqs') }}" class="text-[#5B6478] hover:text-[#0B1020] duration-200">FAQs</a>
                    <a href="{{ route('demo.index') }}" class="text-[#5B6478] hover:text-[#0B1020] duration-200">Request a Demo</a>
                </div>
            </div>
            <div>
                <div class="text-[13.5px] font-bold mb-3.5">Support</div>
                <div class="flex flex-col gap-2.5 text-[13.5px] text-[#5B6478]">
                    <span>(02) 8332-0264</span>
                    <span>info.fcusi@gmail.com</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-11 pt-[22px] pb-2 border-t border-[#E7EAF0] text-[12.5px] text-[#8B93A7]">
            <span>&copy; {{ date('Y') }} FCU Solutions Inc. All rights reserved.</span>
            <span class="flex gap-6">
                {{-- <a href="#f" class="text-[#8B93A7] hover:text-[#5B6478]">Terms &amp; Conditions</a>
                <a href="#f" class="text-[#8B93A7] hover:text-[#5B6478]">Privacy Policy</a> --}}
            </span>
        </div>
    </footer>
</body>
</html>
