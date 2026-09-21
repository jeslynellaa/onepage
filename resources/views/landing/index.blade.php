<x-landing-layout title="OnePage — Welcome">
    {{-- HERO --}}
    <section class="max-w-[1240px] mx-auto grid grid-cols-1 lg:grid-cols-[1fr_1.12fr] gap-10 lg:gap-14 items-center px-5 md:px-10 py-10 lg:py-[56px] lg:pb-[84px]">
        <div>
            <span class="inline-flex items-center bg-[#E8F1FF] text-[#1F6FEB] text-xs font-semibold px-[15px] py-[7px] rounded-full mb-6">Document Control for Modern Teams</span>
            <h1 class="[text-wrap:balance] m-0 mb-5 font-extrabold text-[34px] md:text-[44px] lg:text-[58px] leading-[1.08] lg:leading-[1.04] tracking-[-0.03em] text-[#0B1020]">
                Document Control, Made <span class="text-[#1F6FEB] underline decoration-[3px] underline-offset-[6px]">Simple</span>
            </h1>
            <p class="m-0 mb-8 max-w-[42ch] text-base leading-[1.65] text-[#5B6478]">
                OnePage turns approvals, revisions and audit trails into one clear process, from start to finish.
            </p>
            <div class="flex flex-wrap items-center gap-[18px] mb-10 lg:mb-0">
                <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#0B1020] text-white text-[15px] font-semibold px-[30px] py-4 rounded-full hover:bg-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Get Started</a>
                {{-- <button type="button" @click="videoOpen = true" class="inline-flex items-center gap-2.5 text-[15px] font-semibold text-[#0B1020] hover:text-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">
                    <span class="inline-flex items-center justify-center w-[34px] h-[34px] rounded-full border-[1.5px] border-[#D7DBE4]">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
                    </span>
                    Watch demo
                </button> --}}
            </div>
        </div>

        <div class="relative bg-[#F5F7FA] rounded-[22px] p-[26px] shadow-[0_24px_60px_rgba(11,16,32,.10)] flex items-center justify-center overflow-hidden">
            <div class="floaty absolute top-24 left-24 w-20 h-20 rounded-full bg-white shadow-[0_10px_24px_rgba(11,16,32,.14)] flex items-center justify-center text-white" style="animation: swing-a 5s ease-in-out infinite;" aria-hidden="true">
                <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M13 3H8.2C7.0799 3 6.51984 3 6.09202 3.21799C5.71569 3.40973 5.40973 3.71569 5.21799 4.09202C5 4.51984 5 5.0799 5 6.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.0799 21 8.2 21H10M13 3L19 9M13 3V7.4C13 7.96005 13 8.24008 13.109 8.45399C13.2049 8.64215 13.3578 8.79513 13.546 8.89101C13.7599 9 14.0399 9 14.6 9H19M19 9V10M9 17H11.5M9 13H14M9 9H10M14 21L16.025 20.595C16.2015 20.5597 16.2898 20.542 16.3721 20.5097C16.4452 20.4811 16.5147 20.4439 16.579 20.399C16.6516 20.3484 16.7152 20.2848 16.8426 20.1574L21 16C21.5523 15.4477 21.5523 14.5523 21 14C20.4477 13.4477 19.5523 13.4477 19 14L14.8426 18.1574C14.7152 18.2848 14.6516 18.3484 14.601 18.421C14.5561 18.4853 14.5189 18.5548 14.4903 18.6279C14.458 18.7102 14.4403 18.7985 14.405 18.975L14 21Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
            </div>
            <div class="floaty absolute top-9 right-20 w-10 h-10 rounded-full bg-white shadow-[0_10px_24px_rgba(11,16,32,.14)] flex items-center justify-center text-[#1F6FEB]" style="animation: swing-b 4.5s ease-in-out infinite; animation-delay: .4s;" aria-hidden="true">
                <svg class="w-[17px] h-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
            </div>
            <div class="floaty absolute bottom-11 left-7 w-11 h-11 rounded-full bg-white shadow-[0_10px_24px_rgba(11,16,32,.14)] flex items-center justify-center text-[#10C9B6]" style="animation: swing-c 5.5s ease-in-out infinite; animation-delay: .8s;" aria-hidden="true">
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 12 3.269 3.126A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.874L6 12Zm0 0h7.5" /></svg>
            </div>
            <div class="floaty absolute bottom-28 right-32 w-12 h-12 rounded-full bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] shadow-[0_10px_24px_rgba(31,111,235,.28)]" style="animation: swing-a 4.8s ease-in-out infinite; animation-delay: 1.1s;" aria-hidden="true"></div>

            <div class="relative w-full max-w-[260px] aspect-[9/16] rounded-[28px] overflow-hidden shadow-[0_20px_50px_rgba(11,16,32,.18)] bg-black" x-data="{ muted: true }">
                <video x-ref="heroVideo" class="w-full h-full object-cover" src="{{ asset('videos/onepage-ad.mp4') }}" autoplay muted loop playsinline preload="metadata"></video>
                <button type="button" @click="muted = !muted; $refs.heroVideo.muted = muted" aria-label="Toggle video sound" class="absolute bottom-3 right-3 w-9 h-9 rounded-full bg-[#0B1020]/70 text-white flex items-center justify-center backdrop-blur-sm hover:bg-[#0B1020]/90 duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    <svg x-show="muted" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.25 9.75 19.5 12m0 0 2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6 4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.5A2.25 2.25 0 0 1 2.25 15v-6a2.25 2.25 0 0 1 2.25-2.25h2.25Z" /></svg>
                    <svg x-show="!muted" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.5A2.25 2.25 0 0 1 2.25 15v-6a2.25 2.25 0 0 1 2.25-2.25h2.25Z" /></svg>
                </button>
            </div>
        </div>
    </section>

    {{-- TRUST STRIP --}}
    {{-- <section class="bg-[#F5F7FA] px-5 md:px-10 py-[26px]" aria-label="Customer ratings">
        <div class="max-w-[1240px] mx-auto text-center">
            <div class="text-[13px] text-[#5B6478] mb-4">Trusted by <strong class="text-[#0B1020]">operations teams</strong> across the platform</div>
            <div class="flex flex-wrap justify-center gap-x-[72px] gap-y-4">
                <div class="flex items-center gap-3">
                    <span class="text-[28px] font-extrabold tracking-[-0.03em]">4.8</span>
                    <div>
                        <div class="text-xs font-semibold text-[#0B1020]">Capterra</div>
                        <div class="text-[#F5A623] text-xs tracking-[2px]" aria-hidden="true">★★★★★</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[28px] font-extrabold tracking-[-0.03em]">4.9</span>
                    <div>
                        <div class="text-xs font-semibold text-[#0B1020]">G2</div>
                        <div class="text-[#F5A623] text-xs tracking-[2px]" aria-hidden="true">★★★★★</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[28px] font-extrabold tracking-[-0.03em]">4.8</span>
                    <div>
                        <div class="text-xs font-semibold text-[#0B1020]">Trustpilot</div>
                        <div class="text-[#00B67A] text-xs tracking-[2px]" aria-hidden="true">★★★★★</div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- FEATURES: EMPOWER --}}
    <section id="features" class="scroll-mt-[71px]">
        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-16 lg:pt-24 text-center">
            <h2 class="[text-wrap:balance] m-0 font-extrabold text-[30px] md:text-[36px] lg:text-[42px] leading-[1.12] tracking-[-0.03em]">
                <span class="text-[#1F6FEB]">Empower</span> Your Operations<br class="hidden sm:block" /> Team with us
            </h2>
        </div>

        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-11 flex flex-col gap-[26px]">

            {{-- Plate A: Analytics Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-9 lg:gap-[52px] items-center bg-[#F5F7FA] rounded-[26px] p-6 md:p-11 md:px-12">
                <div class="bg-white rounded-[18px] p-[22px] shadow-[0_8px_26px_rgba(11,16,32,.06)] order-first" aria-hidden="true">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-2xl px-[18px] py-4 border-gray-200 border-2 border mb-[18px]">
                            <div class="text-xs opacity-85 mb-1 uppercase bold">Active</div>
                            <div class="text-[30px] font-extrabold tracking-[-0.03em]">26</div>
                        </div>
                        <div class="rounded-2xl px-[18px] py-4 border-gray-200 border-2 border mb-[18px]">
                            <div class="text-xs opacity-85 mb-1 uppercase bold">Drafts</div>
                            <div class="text-[30px] font-extrabold tracking-[-0.03em]">3</div>
                        </div>
                        <div class="rounded-2xl px-[18px] py-4 border-gray-200 border-2 border mb-[18px]">
                            <div class="text-xs opacity-85 mb-1 uppercase bold">In Review</div>
                            <div class="text-[30px] font-extrabold tracking-[-0.03em]">1</div>
                        </div>
                    </div>
                    <div class="flex items-end gap-2 h-[104px]">
                        <div class="flex-1 h-[36%] bg-[#EEF1F6] rounded-md"></div>
                        <div class="flex-1 h-[52%] bg-[#EEF1F6] rounded-md"></div>
                        <div class="flex-1 h-[44%] bg-[#EEF1F6] rounded-md"></div>
                        <div class="flex-1 h-full bg-[#1F6FEB] rounded-md"></div>
                        <div class="flex-1 h-[70%] bg-[#EEF1F6] rounded-md"></div>
                        <div class="flex-1 h-[84%] bg-[#EEF1F6] rounded-md"></div>
                    </div>
                </div>
                <div>
                    <h3 class="m-0 mb-3.5 font-extrabold text-[26px] md:text-[33px] leading-[1.14] tracking-[-0.03em]">Document <span class="text-[#1F6FEB]">Analytics</span> Dashboard</h3>
                    <p class="m-0 mb-6 text-[15px] leading-[1.65] text-[#5B6478]">Gain real-time visibility into every controlled document — what's current, what's overdue and who owes an approval.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 gap-x-[26px]">
                        <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                            <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                            Track every revision
                        </div>
                        <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                            <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                            Route approvals fast
                        </div>
                        <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                            <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                            See pending reviews
                        </div>
                        <div class="flex items-center gap-2.5 text-[13.5px] font-medium">
                            <span class="w-[19px] h-[19px] rounded-full bg-[#0B1020] text-white flex items-center justify-center flex-none">
                                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </span>
                            Set document codes
                        </div>
                    </div>
                </div>
            </div>

            {{-- Plate B: Track Every Approval --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-9 lg:gap-[52px] items-center bg-[#F5F7FA] rounded-[26px] p-6 md:p-11 md:px-12">
                <div class="order-2 md:order-1">
                    <h3 class="m-0 mb-3.5 font-extrabold text-[26px] md:text-[33px] leading-[1.14] tracking-[-0.03em]"><span class="text-[#1F6FEB]">Track</span> Every Approval Easily</h3>
                    <p class="m-0 mb-6 max-w-[40ch] text-[15px] leading-[1.65] text-[#5B6478]">Effortlessly monitor and manage the work moving through your management system. Stay on top of drafts, reviews and sign-offs with a clear picture of where everything stands.</p>
                    <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#0B1020] text-white text-[14.5px] font-semibold px-7 py-3.5 rounded-full hover:bg-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Get Started</a>
                </div>
                <div class="relative order-1 md:order-2" aria-hidden="true">
                    <div class="bg-white rounded-[18px] px-6 py-[22px] shadow-[0_8px_26px_rgba(11,16,32,.07)] md:ml-[52px]">
                        <div class="text-[13px] font-semibold mb-4">Open Actions</div>
                        <div class="flex items-center gap-[22px]">
                            <svg viewBox="0 0 100 100" class="w-[100px] h-[100px] sm:w-[112px] sm:h-[112px] flex-none -rotate-90">
                                <circle cx="50" cy="50" r="38" fill="none" stroke="#EEF1F6" stroke-width="16" />
                                <circle cx="50" cy="50" r="38" fill="none" stroke="#1F6FEB" stroke-width="16" stroke-dasharray="112 239" />
                                <circle cx="50" cy="50" r="38" fill="none" stroke="#10C9B6" stroke-width="16" stroke-dasharray="72 239" stroke-dashoffset="-112" />
                                <circle cx="50" cy="50" r="38" fill="none" stroke="#B8C4D8" stroke-width="16" stroke-dasharray="45 239" stroke-dashoffset="-184" />
                            </svg>
                            <div class="flex flex-col gap-[11px]">
                                <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#1F6FEB] block"></span><span class="font-semibold">Drafting</span><span class="text-[#8B93A7]">47%</span></div>
                                <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#10C9B6] block"></span><span class="font-semibold">Reviews</span><span class="text-[#8B93A7]">30%</span></div>
                                <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#B8C4D8] block"></span><span class="font-semibold">Approval</span><span class="text-[#8B93A7]">23%</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 md:absolute md:bottom-[-22px] md:left-0 w-full md:w-[194px] bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] rounded-2xl px-[18px] py-4 text-white shadow-[0_16px_34px_rgba(31,111,235,.28)]">
                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="w-6 h-6 rounded-full bg-white/25 flex items-center justify-center">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            </span>
                            <span class="text-[13.5px] font-bold">Cycle time</span>
                        </div>
                        <div class="flex items-baseline gap-[5px]">
                            <span class="text-[25px] font-extrabold tracking-[-0.03em]">2.4</span>
                            <span class="text-xs opacity-85">days avg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-16 lg:pt-24 text-center">
            <h2 class="[text-wrap:balance] m-0 mb-3 font-extrabold text-[30px] md:text-[36px] lg:text-[42px] leading-[1.12] tracking-[-0.03em]">
                <span class="text-[#1F6FEB]">Control</span> Documents Across<br class="hidden sm:block" /> the Organization
            </h2>
            <p class="mx-auto max-w-[52ch] text-[14.5px] leading-[1.6] text-[#5B6478]">One controlled source of truth, distributed to every site, team and auditor who needs the current copy.</p>
        </div>
        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[26px]">

            <div>
                <div class="bg-[#E8F1FF] rounded-[22px] pt-[30px] px-[30px] h-[200px] overflow-hidden flex justify-center" aria-hidden="true">
                    <div class="w-[190px] bg-white rounded-t-2xl p-4 shadow-[0_8px_22px_rgba(11,16,32,.08)]">
                        <div class="text-xs font-bold text-center mb-3.5">Version Control</div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between bg-[#F5F7FA] rounded-lg px-[11px] py-2 text-[11.5px]"><span class="font-semibold">Rev 7</span><span class="text-[#1F6FEB] font-semibold">Current</span></div>
                            <div class="flex items-center justify-between bg-[#F5F7FA] rounded-lg px-[11px] py-2 text-[11.5px] text-[#8B93A7]"><span>Rev 6</span><span>Archived</span></div>
                            <div class="flex items-center justify-between bg-[#F5F7FA] rounded-lg px-[11px] py-2 text-[11.5px] text-[#8B93A7]"><span>Rev 5</span><span>Archived</span></div>
                        </div>
                    </div>
                </div>
                <h4 class="m-0 mt-[22px] mb-2 text-[17px] font-bold tracking-[-0.01em]">Version Management</h4>
                <p class="m-0 text-[13.5px] leading-[1.6] text-[#5B6478]">Every revision kept, superseded copies withdrawn automatically. Nobody works from an old document again.</p>
            </div>

            <div>
                <div class="bg-[#E6FAF6] rounded-[22px] pt-[30px] px-[30px] h-[200px] overflow-hidden flex justify-center" aria-hidden="true">
                    <div class="w-[190px] bg-white rounded-t-2xl p-4 shadow-[0_8px_22px_rgba(11,16,32,.08)]">
                        <div class="text-xs font-bold text-center mb-3.5">Approval Route</div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2.5 text-[11.5px]"><span class="w-[22px] h-[22px] rounded-full bg-[#10C9B6] text-white flex items-center justify-center"><svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg></span>Author</div>
                            <div class="flex items-center gap-2.5 text-[11.5px]"><span class="w-[22px] h-[22px] rounded-full bg-[#10C9B6] text-white flex items-center justify-center"><svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5" /></svg></span>Reviewer</div>
                            <div class="flex items-center gap-2.5 text-[11.5px] text-[#8B93A7]"><span class="w-[22px] h-[22px] rounded-full border-[1.5px] border-dashed border-[#B8C4D8]"></span>Approver</div>
                        </div>
                    </div>
                </div>
                <h4 class="m-0 mt-[22px] mb-2 text-[17px] font-bold tracking-[-0.01em]">Approval Workflows</h4>
                <p class="m-0 text-[13.5px] leading-[1.6] text-[#5B6478]">Route documents to the right people in the right order, with reminders that keep the queue moving.</p>
            </div>

            <div>
                <div class="bg-[#F1EDFF] rounded-[22px] pt-[30px] px-[30px] h-[200px] overflow-hidden flex justify-center" aria-hidden="true">
                    <div class="w-[190px] bg-white rounded-t-2xl p-4 shadow-[0_8px_22px_rgba(11,16,32,.08)]">
                        <div class="text-xs font-bold text-center mb-3.5">Access Control</div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between text-[11.5px]"><span class="flex items-center gap-2"><span class="w-[22px] h-[22px] rounded-full bg-[#DCD3FF] block"></span>Quality</span><span class="text-[#8B93A7]">Edit</span></div>
                            <div class="flex items-center justify-between text-[11.5px]"><span class="flex items-center gap-2"><span class="w-[22px] h-[22px] rounded-full bg-[#CFE0FF] block"></span>Site ops</span><span class="text-[#8B93A7]">View</span></div>
                            <div class="flex items-center justify-between text-[11.5px]"><span class="flex items-center gap-2"><span class="w-[22px] h-[22px] rounded-full bg-[#C9F3EC] block"></span>Auditor</span><span class="text-[#8B93A7]">Read</span></div>
                        </div>
                    </div>
                </div>
                <h4 class="m-0 mt-[22px] mb-2 text-[17px] font-bold tracking-[-0.01em]">Role-Based Access</h4>
                <p class="m-0 text-[13.5px] leading-[1.6] text-[#5B6478]">Only allow certain actions based on a user's account type. Reduce accidental edits and deletions by restricting access.</p>
            </div>
        </div>
    </section>


    {{-- CONTROL DOCUMENTS: 3-up cards --}}
    <section id="benefits" class="scroll-mt-[71px]">
        {{-- IMPROVE --}}
        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-16 lg:pt-24 text-center">
            <h2 class="[text-wrap:balance] m-0 font-extrabold text-[30px] md:text-[36px] lg:text-[42px] leading-[1.12] tracking-[-0.03em]">
                <span class="text-[#1F6FEB]">Improve</span> the Way Your<br class="hidden sm:block" /> Business Runs
            </h2>
        </div>

        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-11 flex flex-col gap-[26px]">

            {{-- Plate C: Excellence --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-9 lg:gap-[52px] items-center bg-[#F5F7FA] rounded-[26px] p-6 md:p-11 md:px-12">
                <div class="flex justify-center items-center h-[200px] md:h-[262px] relative order-first" aria-hidden="true">
                    <div class="absolute w-[220px] h-[140px] md:w-[258px] md:h-[158px] bg-gradient-to-br from-[#0F4FBF] to-[#1F6FEB] rounded-2xl -rotate-[14deg] translate-x-[-14px] translate-y-4 shadow-[0_20px_44px_rgba(11,16,32,.22)]"></div>
                    <div class="relative w-[220px] h-[140px] md:w-[258px] md:h-[158px] bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] rounded-2xl -rotate-6 shadow-[0_20px_44px_rgba(31,111,235,.28)] p-5 text-white flex flex-col justify-between">
                        <div class="text-[11px] tracking-[.14em] uppercase opacity-80">Controlled Copy</div>
                        <div class="text-lg md:text-[19px] font-bold tracking-[.06em]">QMS-04 · REV 7</div>
                        <div class="flex justify-between text-[11.5px] opacity-90"><span>J. Dela Cruz</span><span>Effective 10/24/2025</span></div>
                    </div>
                </div>
                <div>
                    <h3 class="m-0 mb-3.5 font-extrabold text-[26px] md:text-[33px] leading-[1.14] tracking-[-0.03em]">Achieve Operational <span class="text-[#1F6FEB]">Excellence</span></h3>
                    <p class="m-0 mb-6 text-[15px] leading-[1.65] text-[#5B6478]">Put your management system to work. Reviews and reporting connect to the documents they govern.</p>
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-3.5">
                            <span class="w-[34px] h-[34px] rounded-[10px] bg-[#E6FAF6] text-[#0E9E8E] flex items-center justify-center flex-none">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            </span>
                            <div>
                                <div class="text-[14.5px] font-bold mb-1">Email Notifications</div>
                                <div class="text-[13px] leading-[1.55] text-[#5B6478]">Reviewers and approvers get notified the moment a document needs their sign-off.</div>
                            </div>
                        </div>
                        <div class="flex gap-3.5">
                            <span class="w-[34px] h-[34px] rounded-[10px] bg-[#E8F1FF] text-[#1F6FEB] flex items-center justify-center flex-none">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            </span>
                            <div>
                                <div class="text-[14.5px] font-bold mb-1">Audit Trail</div>
                                <div class="text-[13px] leading-[1.55] text-[#5B6478]">Every status change, comment and approval is timestamped.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Plate D: Integrate --}}
            {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-9 lg:gap-[52px] items-center bg-[#F5F7FA] rounded-[26px] p-6 md:p-11 md:px-12">
                <div>
                    <h3 class="m-0 mb-3.5 font-extrabold text-[26px] md:text-[33px] leading-[1.14] tracking-[-0.03em]"><span class="text-[#1F6FEB]">Integrate</span> With Your Favorite Tools</h3>
                    <p class="m-0 mb-6 max-w-[40ch] text-[15px] leading-[1.65] text-[#5B6478]">OnePage sits alongside the tools your teams already use — single sign-on, file storage, email and the reporting stack.</p>
                    <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#0B1020] text-white text-[14.5px] font-semibold px-7 py-3.5 rounded-full hover:bg-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">Explore Integrations</a>
                </div>

                <!-- Orbit: desktop -->
                <div class="hidden md:block relative h-[240px]" aria-hidden="true">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[76px] h-[76px] rounded-[22px] bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] shadow-[0_16px_34px_rgba(31,111,235,.3)] flex items-center justify-center text-white text-2xl font-extrabold">1</div>
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[190px] h-[190px] border-[1.5px] border-dashed border-[#D7DBE4] rounded-full"></div>
                    <div class="absolute left-[calc(50%-22px)] top-[calc(50%-117px)] w-11 h-11 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] flex items-center justify-center text-[11px] font-bold text-[#5B6478]">SSO</div>
                    <div class="absolute left-[calc(50%+73px)] top-[calc(50%-22px)] w-11 h-11 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] flex items-center justify-center text-[11px] font-bold text-[#5B6478]">M365</div>
                    <div class="absolute left-[calc(50%-22px)] top-[calc(50%+73px)] w-11 h-11 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] flex items-center justify-center text-[11px] font-bold text-[#5B6478]">BI</div>
                    <div class="absolute left-[calc(50%-117px)] top-[calc(50%-22px)] w-11 h-11 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] flex items-center justify-center text-[11px] font-bold text-[#5B6478]">SFTP</div>
                    <div class="absolute left-[calc(50%+45px)] top-[calc(50%-90px)] w-10 h-10 rounded-[13px] bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] flex items-center justify-center text-[10.5px] font-bold text-[#5B6478]">API</div>
                    <div class="absolute left-[calc(50%-85px)] top-[calc(50%+50px)] w-10 h-10 rounded-[13px] bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] flex items-center justify-center text-[10.5px] font-bold text-[#5B6478]">SMTP</div>
                </div>

                <!-- Orbit: mobile fallback (reflowed, not positioned) -->>
                <div class="md:hidden flex flex-wrap gap-2.5" aria-hidden="true">
                    <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] text-[11px] font-bold text-[#5B6478]">SSO</span>
                    <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] text-[11px] font-bold text-[#5B6478]">M365</span>
                    <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] text-[11px] font-bold text-[#5B6478]">BI</span>
                    <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] text-[11px] font-bold text-[#5B6478]">SFTP</span>
                    <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] text-[11px] font-bold text-[#5B6478]">API</span>
                    <span class="inline-flex items-center justify-center h-10 px-4 rounded-2xl bg-white shadow-[0_6px_18px_rgba(11,16,32,.10)] text-[11px] font-bold text-[#5B6478]">SMTP</span>
                </div>
            </div> --}}
        </div>
    </section>

    {{-- ABOUT --}}
    <section id="about" class="scroll-mt-[71px]">
        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-16 lg:pt-24 text-center">
            <h2 class="[text-wrap:balance] m-0 mb-3 font-extrabold text-[30px] md:text-[36px] lg:text-[42px] leading-[1.12] tracking-[-0.03em]">
                About <span class="text-[#1F6FEB]">FCU Solutions,</span> <span class="text-[#10C9B6]">Inc.</span>
            </h2>
            <p class="mx-auto max-w-[56ch] text-[14.5px] leading-[1.6] text-[#5B6478]">OnePage is built and run by FCU Solutions Inc., a management-systems consultancy with more than two decades of management systems and organizational-development work.</p>
        </div>

        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-9 lg:gap-[52px] items-center bg-[#F5F7FA] rounded-[26px] p-6 md:p-11 md:px-12">
                <div>
                    <img src="{{ asset('img/fcu-logo.jpg') }}" alt="FCU Solutions Inc." class="h-10 w-auto mb-6 rounded-lg">
                    <p class="m-0 mb-4 text-[15px] leading-[1.65] text-[#5B6478]">Backed by a team of professionals with diverse expertise, FCU created OnePage to embody its mission of driving positive organizational change through practical, innovative solutions.</p>
                    <p class="m-0 mb-6 text-[15px] leading-[1.65] text-[#5B6478]">Built on a legacy of client partnerships, OnePage reflects FCU's commitment to simplifying ISO compliance while helping businesses grow and succeed.</p>
                    <a href="https://www.fcusolutions.org/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-[14.5px] font-semibold text-[#0B1020] hover:text-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1F6FEB]">
                        Visit fcusolutions.org
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9" /></svg>
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-4" aria-hidden="true">
                    <div class="bg-white rounded-2xl px-5 py-6 shadow-[0_8px_26px_rgba(11,16,32,.06)] text-center">
                        <div class="text-[34px] font-extrabold tracking-[-0.03em] text-[#1F6FEB]">{{ (int) \Carbon\Carbon::parse('1998-08-01')->diffInYears(now()) }}</div>
                        <div class="text-[13px] text-[#5B6478] mt-1">Years in the industry</div>
                    </div>
                    <div class="bg-white rounded-2xl px-5 py-6 shadow-[0_8px_26px_rgba(11,16,32,.06)] text-center">
                        <div class="text-[34px] font-extrabold tracking-[-0.03em] text-[#10C9B6]">400+</div>
                        <div class="text-[13px] text-[#5B6478] mt-1">Clients served</div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] text-center row-span-2 overflow-hidden">
                        <img src="{{ asset('img/team-hands.png') }}" 
                            alt="team hands" 
                            class="inset-0 w-full h-full object-cover">
                    </div>
                    <div class="bg-white rounded-2xl px-5 py-6 shadow-[0_8px_26px_rgba(11,16,32,.06)] text-center col-span-2">
                        <div class="text-[15px] font-bold text-[#0B1020]">Management systems &amp; organizational development consultancy</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Values strip --}}
        <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-[26px]">
            <div class="bg-[#141A33] rounded-[26px] px-6 md:px-11 py-8 md:py-10">
                <div class="text-center text-white/50 text-xs font-semibold tracking-[.14em] uppercase mb-6">Our Values</div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-x-4 gap-y-6 text-center">
                    <div class="text-white text-[13.5px] font-semibold">Respect</div>
                    <div class="text-white text-[13.5px] font-semibold">Commitment &amp; Passion</div>
                    <div class="text-white text-[13.5px] font-semibold">Integrity</div>
                    <div class="text-white text-[13.5px] font-semibold">Personal Growth &amp; Learning</div>
                    <div class="text-white text-[13.5px] font-semibold">Excellence</div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-16 lg:pt-20">
        <div class="grid grid-cols-1 md:grid-cols-[.92fr_1.08fr] gap-8 lg:gap-10 items-center bg-[#141A33] rounded-[26px] p-8 md:p-11 lg:p-[52px] lg:pl-11 overflow-hidden">
            <div class="relative order-2 md:order-1">
                <div class="flex flex-col gap-3 md:relative md:h-[250px]" aria-hidden="true">
                    <div class="md:absolute md:left-0 md:top-1.5 w-full md:w-[200px] bg-white rounded-2xl px-[18px] py-4 shadow-[0_16px_38px_rgba(0,0,0,.3)]">
                        <div class="text-[12.5px] font-bold mb-2.5">Pending reviews</div>
                        <div class="text-[28px] font-extrabold tracking-[-0.03em] text-[#1F6FEB]">0</div>
                    </div>
                    <div class="md:absolute md:left-[118px] md:top-[98px] w-full md:w-[214px] bg-white rounded-2xl px-[18px] py-4 shadow-[0_16px_38px_rgba(0,0,0,.3)]">
                        <div class="text-[12.5px] font-bold mb-2.5">Pending approvals</div>
                        <div class="text-[28px] font-extrabold tracking-[-0.03em] text-[#1F6FEB]">2</div>
                    </div>
                    <div class="md:absolute md:left-3.5 md:top-[186px] w-full md:w-[180px] bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] rounded-2xl px-[18px] py-3.5 text-white shadow-[0_16px_38px_rgba(0,0,0,.3)]">
                        <div class="text-[11.5px] opacity-85 mb-1">Ave. Document Cycle</div>
                        <div class="text-[22px] font-extrabold tracking-[-0.03em]">21.4 hrs</div>
                    </div>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <h2 class="[text-wrap:balance] m-0 mb-4 font-extrabold text-[30px] md:text-[36px] lg:text-[40px] leading-[1.1] tracking-[-0.03em] text-white">Ready to Run your Document Process <span class="text-[#3DE0C8]">Better</span> with us</h2>
                <p class="m-0 mb-[30px] max-w-[44ch] text-[15px] leading-[1.65] text-white/68">OnePage, where document management meets simplicity and efficiency. Backed by <a href="https://www.fcusolutions.org/" target="_blank" class="underline">FCU Solutions</a>  — more than 2 decades, and hundreds of clients.</p>
                <a href="{{ route('demo.index') }}" class="inline-flex items-center bg-[#1F6FEB] text-white text-[15px] font-semibold px-8 py-[15px] rounded-full hover:bg-[#3DE0C8] hover:text-[#0B1020] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Get Started</a>
            </div>
        </div>
    </div>

    {{-- ACTION CARDS --}}
    {{-- <div class="max-w-[1240px] mx-auto px-5 md:px-10 pt-[26px] grid grid-cols-1 md:grid-cols-2 gap-[26px]">
        <div class="bg-[#1F6FEB] rounded-[26px] p-8 md:p-10 text-white">
            <span class="inline-flex items-center justify-center w-[46px] h-[46px] rounded-full bg-white text-[#1F6FEB] mb-[26px]">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
            </span>
            <h3 class="m-0 mb-2 text-[26px] md:text-[30px] font-extrabold tracking-[-0.03em]">Talk to Us</h3>
            <p class="m-0 mb-6 text-sm leading-[1.6] text-white/80">Fifteen minutes with someone who has implemented this 400 times.</p>
            <a href="{{ route('demo.index') }}" class="inline-flex items-center border-[1.5px] border-white/60 text-white text-sm font-semibold px-[26px] py-3 rounded-full hover:bg-white hover:text-[#1F6FEB] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Book a Call</a>
        </div>
        <div class="bg-[#10C9B6] rounded-[26px] p-8 md:p-10 text-[#06342F]">
            <div class="flex items-start justify-between gap-5 mb-[26px]">
                <span class="inline-flex items-center justify-center w-[46px] h-[46px] rounded-full bg-[#06342F] text-[#3DE0C8] flex-none">
                    <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                </span>
                <button type="button" @click="videoOpen = true" aria-label="Play demo video" x-data="{ observe() { new IntersectionObserver((entries) => { if (entries[0].isIntersecting) { $refs.previewVid.play().catch(() => {}); } }, { threshold: 0.4 }).observe($el); } }" x-init="observe()" class="relative flex-none w-[72px] h-[104px] rounded-xl overflow-hidden shadow-[0_10px_24px_rgba(6,52,47,.28)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#06342F]">
                    <video x-ref="previewVid" class="w-full h-full object-cover" src="{{ asset('videos/onepage-ad.mp4') }}" muted loop playsinline preload="metadata"></video>
                    <span class="absolute inset-0 flex items-center justify-center bg-[#06342F]/20">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/90 text-[#06342F]">
                            <svg class="w-2.5 h-2.5 translate-x-[1px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
                        </span>
                    </span>
                </button>
            </div>
            <h3 class="m-0 mb-2 text-[26px] md:text-[30px] font-extrabold tracking-[-0.03em]">Watch a Demo</h3>
            <p class="m-0 mb-6 text-sm leading-[1.6] text-[#06342F]/72">Four minutes, no form — see the register, a revision and an audit export.</p>
            <button type="button" @click="videoOpen = true" class="inline-flex items-center bg-[#06342F] text-white text-sm font-semibold px-[26px] py-3 rounded-full hover:bg-[#0B1020] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#06342F]">Watch Now</button>
        </div>
    </div> --}}
</x-landing-layout>
