<x-landing-layout title="OnePage — Pricing">
    {{-- PRICING HERO --}}
    <section class="max-w-[1240px] mx-auto px-5 md:px-10 pt-14 lg:pt-20 pb-2 text-center">
        <h1 class="[text-wrap:balance] mx-auto m-0 mb-4 font-extrabold text-[34px] md:text-[44px] lg:text-[52px] leading-[1.08] tracking-[-0.03em] text-[#0B1020]">
            <span class="text-[#1F6FEB]">Pricing</span> Offers
        </h1>
        <p class="mx-auto max-w-[52ch] text-[15px] leading-[1.65] text-[#5B6478]">One plan, every feature included. Pick a billing cycle and get your document control under control.</p>
    </section>

    {{-- PRICING CARD --}}
    <section class="max-w-[1240px] mx-auto px-5 md:px-10 pt-10 pb-16 lg:pb-24" x-data="{ period: 'monthly', prices: { monthly: { amount: '2,480', label: '/mo' }, yearly: { amount: '28,800', label: '/yr' }, triennial: { amount: '80,880', label: '/3 yrs' } } }">
        <div class="max-w-[420px] mx-auto bg-[#F5F7FA] rounded-[26px] p-[26px] md:p-9 shadow-[0_24px_60px_rgba(11,16,32,.10)]">
            <div class="text-center">
                <span class="inline-flex items-center bg-white text-[#1F6FEB] text-xs font-semibold px-[15px] py-[7px] rounded-full shadow-[0_2px_10px_rgba(11,16,32,.05)]">Standard Plan</span>

                <div class="mt-5 flex items-baseline justify-center gap-1.5">
                    <span class="text-lg font-semibold text-[#5B6478]">₱</span>
                    <span class="text-[46px] font-extrabold tracking-[-0.03em] text-[#0B1020]" x-text="prices[period].amount"></span>
                    <span class="text-sm font-medium text-[#8B93A7]" x-text="prices[period].label"></span>
                </div>

                <div class="mt-6 inline-flex p-1 bg-white rounded-full shadow-[0_2px_10px_rgba(11,16,32,.05)]">
                    <button type="button" @click="period = 'monthly'" :class="period === 'monthly' ? 'bg-[#0B1020] text-white' : 'text-[#5B6478] hover:text-[#0B1020]'" class="px-4 py-2 text-xs font-semibold rounded-full duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">1 Month</button>
                    <button type="button" @click="period = 'yearly'" :class="period === 'yearly' ? 'bg-[#0B1020] text-white' : 'text-[#5B6478] hover:text-[#0B1020]'" class="px-4 py-2 text-xs font-semibold rounded-full duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">1 Year</button>
                    <button type="button" @click="period = 'triennial'" :class="period === 'triennial' ? 'bg-[#0B1020] text-white' : 'text-[#5B6478] hover:text-[#0B1020]'" class="px-4 py-2 text-xs font-semibold rounded-full duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">3 Years</button>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3.5">
                <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                    <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </span>
                    Up to 15 users included
                </div>
                <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                    <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </span>
                    Every feature included
                </div>
                <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                    <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                        <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </span>
                    Accessible on any device
                </div>
            </div>

            <div class="mt-6 bg-white rounded-2xl p-4 text-center shadow-[0_2px_10px_rgba(11,16,32,.05)]">
                <div class="text-[11px] font-semibold text-[#1F6FEB] uppercase tracking-[.08em] mb-1">Need more team members?</div>
                <div class="text-[13px] text-[#5B6478]">Add extra users for just <span class="font-bold text-[#0B1020]">₱158</span> per user / month.</div>
            </div>

            <a href="{{ route('demo.index') }}" class="mt-6 flex items-center justify-center bg-[#0B1020] text-white text-[15px] font-semibold py-3.5 rounded-full hover:bg-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Get Started</a>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <div class="max-w-[1240px] mx-auto px-5 md:px-10 pb-16 lg:pb-24">
        <div class="bg-[#141A33] rounded-[26px] p-8 md:p-11 lg:p-[52px] text-center">
            <h2 class="[text-wrap:balance] mx-auto m-0 mb-4 font-extrabold text-[26px] md:text-[32px] leading-[1.15] tracking-[-0.03em] text-white max-w-[36ch]">Still deciding? Let's <span class="text-[#3DE0C8]">talk it through</span>.</h2>
            {{-- <p class="mx-auto m-0 mb-8 max-w-[46ch] text-[15px] leading-[1.65] text-white/68">Fifteen minutes with someone who has set this up for hundreds of clients — no pressure, no obligation.</p> --}}
            <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#1F6FEB] text-white text-[15px] font-semibold px-8 py-[15px] rounded-full hover:bg-[#3DE0C8] hover:text-[#0B1020] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Book a Call</a>
        </div>
    </div>
</x-landing-layout>
