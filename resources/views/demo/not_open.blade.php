<x-landing-layout title="OnePage — Your demo opens soon">
    @php
        $opensAt = $company->demo_starts_at;
        $closesAt = $company->demo_expires_at;
    @endphp

    <section class="max-w-[1240px] mx-auto px-5 md:px-10 pt-14 lg:pt-20 pb-16 lg:pb-24">
        <div class="max-w-[520px] mx-auto bg-[#F5F7FA] rounded-[26px] p-[26px] md:p-10 shadow-[0_24px_60px_rgba(11,16,32,.10)] text-center">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#EEF4FF] text-[#1F6FEB] mb-5">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
            </span>

            <h1 class="[text-wrap:balance] m-0 mb-3 font-extrabold text-[26px] md:text-[30px] leading-[1.15] tracking-[-0.02em] text-[#0B1020]">
                Your demo opens soon
            </h1>

            <p class="m-0 mb-6 text-[15px] leading-[1.65] text-[#5B6478]">
                The <strong class="text-[#0B1020]">{{ $company->name }}</strong> workspace is being kept ready for you.
                Come back to this same link when it opens — no sign-in needed.
            </p>

            <div class="bg-white rounded-2xl border border-[#E7EAF0] px-5 py-4 mb-6">
                <div class="text-[12px] font-bold uppercase tracking-[0.08em] text-[#8B93A7] mb-1">Opens</div>
                <div class="text-[18px] font-extrabold text-[#0B1020]">
                    {{ $opensAt->format('l, F j, Y') }} at {{ $opensAt->format('g:i A') }}
                </div>
                <div class="text-[13px] text-[#5B6478] mt-1">
                    Philippine Time · {{ $opensAt->diffForHumans(['parts' => 2]) }}
                </div>
                @if ($closesAt)
                    <div class="text-[13px] text-[#5B6478] mt-3 pt-3 border-t border-[#E7EAF0]">
                        Available until {{ $closesAt->format('l, F j \a\t g:i A') }}
                    </div>
                @endif
            </div>

            <p class="m-0 text-[13px] leading-[1.6] text-[#8B93A7]">
                Need a different time? Reply to the email this link came in and we'll reschedule.
            </p>
        </div>
    </section>
</x-landing-layout>
