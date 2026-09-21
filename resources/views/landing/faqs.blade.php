<x-landing-layout title="OnePage — FAQs">
    {{-- FAQS HERO --}}
    <section class="max-w-[1240px] mx-auto px-5 md:px-10 pt-14 lg:pt-20 pb-2 text-center">
        <h1 class="[text-wrap:balance] mx-auto m-0 mb-4 font-extrabold text-[34px] md:text-[44px] lg:text-[52px] leading-[1.08] tracking-[-0.03em] text-[#0B1020]">
            Frequently Asked <span class="text-[#1F6FEB]">Questions</span>
        </h1>
        <p class="mx-auto max-w-[52ch] text-[15px] leading-[1.65] text-[#5B6478]">Answers to what teams usually ask before getting started.</p>
    </section>

    {{-- FAQ ACCORDION --}}
    <section class="max-w-[760px] mx-auto px-5 md:px-10 pt-10 pb-16 lg:pb-24">
        <div class="flex flex-col gap-3.5">

            <div class="bg-[#F5F7FA] rounded-2xl overflow-hidden" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#1F6FEB]" :aria-expanded="open">
                    <span class="text-[15px] font-bold text-[#0B1020]">What is OnePage?</span>
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 flex-none text-[#5B6478] duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                </button>
                <div x-show="open" x-transition class="px-5 pb-4 text-[13.5px] leading-[1.6] text-[#5B6478]">
                    OnePage is a document management system designed by FCU Solutions Inc. to streamline document creation, review, approval, monitoring and storage.
                </div>
            </div>

            <div class="bg-[#F5F7FA] rounded-2xl overflow-hidden" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#1F6FEB]" :aria-expanded="open">
                    <span class="text-[15px] font-bold text-[#0B1020]">Who can use OnePage?</span>
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 flex-none text-[#5B6478] duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                </button>
                <div x-show="open" x-transition class="px-5 pb-4 text-[13.5px] leading-[1.6] text-[#5B6478]">
                    Authorized users within the organization such as authors, reviewers, approvers and document controllers can use the system based on their assigned roles and permissions.
                </div>
            </div>

            <div class="bg-[#F5F7FA] rounded-2xl overflow-hidden" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#1F6FEB]" :aria-expanded="open">
                    <span class="text-[15px] font-bold text-[#0B1020]">Can I track document status in real time?</span>
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 flex-none text-[#5B6478] duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                </button>
                <div x-show="open" x-transition class="px-5 pb-4 text-[13.5px] leading-[1.6] text-[#5B6478]">
                    Yes, OnePage lets you monitor document progress and view the current status throughout the workflow.
                </div>
            </div>

        </div>
    </section>

    {{-- CTA BANNER --}}
    <div class="max-w-[1240px] mx-auto px-5 md:px-10 pb-16 lg:pb-24">
        <div class="bg-[#141A33] rounded-[26px] p-8 md:p-11 lg:p-[52px] text-center">
            <h2 class="[text-wrap:balance] mx-auto m-0 mb-4 font-extrabold text-[26px] md:text-[32px] leading-[1.15] tracking-[-0.03em] text-white max-w-[36ch]">Still have <span class="text-[#3DE0C8]">questions</span>?</h2>
            {{-- <p class="mx-auto m-0 mb-8 max-w-[46ch] text-[15px] leading-[1.65] text-white/68">Let's talk.</p> --}}
            <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#1F6FEB] text-white text-[15px] font-semibold px-8 py-[15px] rounded-full hover:bg-[#3DE0C8] hover:text-[#0B1020] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Book a Call</a>
        </div>
    </div>
</x-landing-layout>
