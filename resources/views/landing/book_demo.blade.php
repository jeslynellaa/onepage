<x-landing-layout title="OnePage — Book a Demo">
    {{-- BOOK DEMO HERO --}}
    <section class="max-w-[1240px] mx-auto px-5 md:px-10 pt-14 lg:pt-20 pb-2 text-center">
        <h1 class="[text-wrap:balance] mx-auto m-0 mb-4 font-extrabold text-[34px] md:text-[44px] lg:text-[52px] leading-[1.08] tracking-[-0.03em] text-[#0B1020]">
            See OnePage <span class="text-[#1F6FEB]">In Action</span>
        </h1>
        <p class="mx-auto max-w-[52ch] text-[15px] leading-[1.65] text-[#5B6478]">See how our platform can streamline your document workflows and team collaboration.</p>
    </section>

    {{-- FORM CARD --}}
    <section class="max-w-[1240px] mx-auto px-5 md:px-10 pt-10 pb-16 lg:pb-24">
        <div class="max-w-[460px] mx-auto bg-[#F5F7FA] rounded-[26px] p-[26px] md:p-9 shadow-[0_24px_60px_rgba(11,16,32,.10)]">

            @if (session('success'))
                <div class="text-center py-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#E6FAF6] text-[#0E9E8E] mb-4">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <h3 class="m-0 mb-2 text-[20px] font-extrabold tracking-[-0.02em] text-[#0B1020]">Demo Request Received!</h3>
                    <p class="m-0 text-[14px] leading-[1.6] text-[#5B6478]">{{ session('success') }}</p>
                </div>
            @else
                <form method="POST" action="{{ route('demo.store') }}" class="flex flex-col gap-5">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-[#FEECEC] text-[#B42318] rounded-2xl p-4 text-[13px] leading-[1.6]">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="name" class="block text-[13px] font-semibold text-[#0B1020] mb-1.5">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-white border border-[#D7DBE4] rounded-xl text-[14px] text-[#0B1020] placeholder:text-[#8B93A7] focus:outline-none focus:ring-2 focus:ring-[#1F6FEB] focus:border-[#1F6FEB] duration-200" placeholder="Juan dela Cruz">
                    </div>

                    <div>
                        <label for="email" class="block text-[13px] font-semibold text-[#0B1020] mb-1.5">Work Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-white border border-[#D7DBE4] rounded-xl text-[14px] text-[#0B1020] placeholder:text-[#8B93A7] focus:outline-none focus:ring-2 focus:ring-[#1F6FEB] focus:border-[#1F6FEB] duration-200" placeholder="juan@company.com">
                    </div>

                    <div>
                        <label for="company" class="block text-[13px] font-semibold text-[#0B1020] mb-1.5">Company Name</label>
                        <input type="text" id="company" name="company" value="{{ old('company') }}" required class="w-full px-4 py-3 bg-white border border-[#D7DBE4] rounded-xl text-[14px] text-[#0B1020] placeholder:text-[#8B93A7] focus:outline-none focus:ring-2 focus:ring-[#1F6FEB] focus:border-[#1F6FEB] duration-200" placeholder="ACME Corp.">
                    </div>

                    <div>
                        <label for="teamSize" class="block text-[13px] font-semibold text-[#0B1020] mb-1.5">Estimated Users Needed</label>
                        <select id="teamSize" name="teamSize" class="w-full px-4 py-3 bg-white border border-[#D7DBE4] rounded-xl text-[14px] text-[#0B1020] focus:outline-none focus:ring-2 focus:ring-[#1F6FEB] focus:border-[#1F6FEB] duration-200">
                            <option value="1-15" {{ old('teamSize') == '1-15' ? 'selected' : '' }}>1 - 15 users (Standard Plan)</option>
                            <option value="15+" {{ old('teamSize') == '15+' ? 'selected' : '' }}>More than 15 users</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="demo_date" class="block text-[13px] font-semibold text-[#0B1020] mb-1.5">Preferred Date</label>
                            <input type="date" id="demo_date" name="demo_date" value="{{ old('demo_date') }}" required class="demo-date-picker w-full px-4 py-3 bg-white border border-[#D7DBE4] rounded-xl text-[14px] text-[#0B1020] focus:outline-none focus:ring-2 focus:ring-[#1F6FEB] focus:border-[#1F6FEB] duration-200" placeholder="Select a date">
                        </div>
                        <div>
                            <label for="demo_time" class="block text-[13px] font-semibold text-[#0B1020] mb-1.5">Preferred Time</label>
                            <select id="demo_time" name="demo_time" class="w-full px-4 py-3 bg-white border border-[#D7DBE4] rounded-xl text-[14px] text-[#0B1020] focus:outline-none focus:ring-2 focus:ring-[#1F6FEB] focus:border-[#1F6FEB] duration-200">
                                <option value="morning" {{ old('demo_time') == 'morning' ? 'selected' : '' }}>Morning (9AM–12PM)</option>
                                <option value="afternoon" {{ old('demo_time') == 'afternoon' ? 'selected' : '' }}>Afternoon (1PM–5PM)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="mt-2 flex items-center justify-center bg-[#0B1020] text-white text-[15px] font-semibold py-3.5 rounded-full hover:bg-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Schedule My Demo</button>
                </form>
            @endif
        </div>
    </section>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const minDemoDate = new Date();
        minDemoDate.setDate(minDemoDate.getDate() + 7);

        flatpickr('.demo-date-picker', {
            minDate: minDemoDate,
            disable: [
                function(date) {
                    const dayOfWeek = date.getDay();
                    const dayOfMonth = date.getDate();

                    if (dayOfWeek === 0) return true;
                    if (dayOfWeek === 1 && dayOfMonth <= 7) return true;

                    return false;
                }
            ]
        });
    </script>
</x-landing-layout>
