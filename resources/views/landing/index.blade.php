<x-landing-layout>
    <div>
        <div id="hero-section" class="mx-auto px-20 bg-gradient-to-b from-gray-100 to-blue-300 h-130">
            <div class="mt-16 h-110 pt-3 flex flex-row">
                <div class="flex flex-col w-1/2 justify-around h-95">
                    <div class="rounded-3xl py-1 px-6 bg-blue-100 w-min whitespace-nowrap border border-blue-700 font-semibold">
                        <i class="fa-regular fa-circle-check font-bold"></i>
                        Compliance Made Simple
                    </div>
                    <h2 class="text-4xl font-bold mt-4 text-[#001f3f]">
                        Automate Your 
                        <span class="bg-gradient-to-r from-blue-700 via-[#74c365] to-blue-500 bg-clip-text text-transparent">Document Handling Processes</span> 
                        Effortlessly</h2>
                    <p class="text-lg font-medium max-w-110">
                        Transform your business compliance with our intelligent automation platform. Optimize your approval process while reducing manual error and ensuring continuous compliance.
                    </p>
                    {{-- <div class="flex gap-6">
                        <button class="rounded-xl w-48 text-center py-2 text-white bg-blue-500 hover:bg-[#74c365] cursor-pointer duration-300 whitespace-nowrap">Contact Us</button>
                        <button class="rounded-xl w-48 text-center py-2 border border-blue-500 hover:border-[#74c365] cursor-pointer duration-300 border-2 whitespace-nowrap">Schedule a Demo</button>
                    </div> --}}
                </div>
                <div class="flex w-1/2 justify-center mx-auto">
                    <img src="{{ asset('img/pc-icon.png') }}" alt="pc-icon" class="h-full">
                </div>
            </div>
        </div>
        <div id="features-section" class="bg-gradient-to-b from-blue-300 to-gray-100 min-h-screen">
            <div class="relative bg-gradient-to-b from-blue-400 to-blue-600 rounded-t-[7rem] rounded-b-3xl flex flex-col text-center px-20 py-12 overflow-hidden">
                <!-- background image layer -->
                <div class="absolute inset-0 bg-cover bg-center"
                     style="background-image: url('/img/bg-line.png');
                            background-size: contain;
                            background-position: center;
                            background-repeat: no-repeat;"
                ></div>
                
                <!-- content -->
                <div class="relative">
                    <p class="text-2xl font-semibold text-white">Powerful Features for <span class="underline">Complete Control</span></p>
                    <p class="text-xl font-medium md:px-15 mt-3">Everything you need to streamline your document processes, ensure compliance, and drive continuous improvement across your organization.</p>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-regular fa-file text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Smart Docs
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Document Management
                            </div>
                            <div>
                                <p>
                                    Centralized document control with version management, approval workflows, and automated distribution.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-diagram-project text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Workflow
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Process Optimization
                            </div>
                            <div>
                                <p>
                                    Make your processes work for you and not the other way around. Enhance business efficiency across all processes.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-chart-simple text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Live Data
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Real-time Analytics
                            </div>
                            <div>
                                <p>
                                    Live dashboards and reporting that provide instant insights into your business status and performance metrics across timelines.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-book-open text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Actionable Insights
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Reports Generation
                            </div>
                            <div>
                                <p>
                                    Create automated reports that turn business data into clear insights for smarter decisions and tracking performance.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-regular fa-bell text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Timely Alerts
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Notifications & Reminders
                            </div>
                            <div>
                                <p>
                                    Stay on top of tasks, approvals, and document reviews with timely alerts and reminders that keep processes moving smoothly.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-list-ul text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Traceability
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Audit Trail
                            </div>
                            <div>
                                <p>
                                    Maintain a secure record of all actions and changes, ensuring transparency and quick troubleshooting.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 max-h-120 shadow-2xl justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-lock text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Secure Access
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Access Control
                            </div>
                            <div>
                                <p>
                                    Role-based permissions that protect sensitive information while giving the right people the right level of access.
                                </p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-white/80 p-6 gap-6 flex flex-col text-left min-h-80 justify-between">
                            <div class="flex justify-between items-center">
                                <div class="rounded-lg bg-gradient-to-b from-green-200 to-green-300 h-12 w-12 flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-award text-3xl font-bold"></i>
                                </div>
                                <div class="rounded-full px-4 py-1 text-center border border-[#74c365] bg-green-100 text-xs font-semibold text-green-800">
                                    Compliance Ready
                                </div>
                            </div>
                            <div class="font-medium text-xl">
                                Compliance Tracking
                            </div>
                            <div>
                                <p>
                                    Monitor regulatory requirements and ISO standards with built-in tracking to ensure nothing falls through the cracks.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="benefits-section" class="bg-gray-100 p-20">
            <div class="flex flex-col lg:flex-row justify-between">
                <div class="p-5 bg-white rounded-3xl h-110 shadow-2xl justify-center items-center mx-auto flex">
                    <img src="{{ asset('img/group-working.png') }}" alt="group working" class="rounded-2xl h-full">
                </div>
                <div class="p-3 lg:w-1/2 flex flex-col justify-around lg:pr-15">
                    <h2 class="text-4xl font-bold mt-4 text-[#001f3f]"> Reshape Your <br> Document Operations</h2>
                    <p class="text-xl font-medium text-[#001f3f]">Revolutionize your document processes with our intelligent automation platform built for efficiency, accuracy, and ease.</p>
                    
                    <div class="flex flex-col gap-3 mt-3 text-sm mb-3 text-[#001f3f]">
                        <div>
                            <div class="flex items-baseline text-lg gap-2">
                                <i class="fa-regular fa-star"></i>
                                <span>Eliminate manual document tracking</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-baseline text-lg gap-2">
                                <i class="fa-regular fa-star"></i>
                                <span>Automate corrective action workflows</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-baseline text-lg gap-2">
                                <i class="fa-regular fa-star"></i>
                                <span>Real-time approval process monitoring</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-baseline text-lg gap-2">
                                <i class="fa-regular fa-star"></i>
                                <span>Centralized hub for your documents</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-20 py-10">
            <div id="about-section" class="text-center">
                <p class="text-2xl font-bold">
                    About <a href="https://www.fcusolutions.org/" class="hover:text-blue-700">FCU Solutions Inc.</a>
                </p>
                <p class="text-lg lg:text-xl font-medium md:px-12 lg:px-45 mt-3">OnePage is developed by FCU Solutions Inc., a trusted consultancy with over two decades of experience in management systems and organizational development.</p>

                <div class="grid grid-cols-6 lg:grid-cols-12 gap-2 mt-3">
                    <div class="relative shadow-lg bg-blue-500 lg:col-span-3 col-span-3 rounded-tl-[3rem] text-white overflow-hidden flex justify-center items-center xl:min-h-34">
                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/img/lines.png'); background-size: cover;background-position: center; background-repeat: no-repeat;"></div>

                        <div class="relative flex flex-row justify-center items-center [text-shadow:0_2px_6px_rgba(0,0,0,0.75)] gap-3 text-left">
                            <div class="text-3xl lg:text-[52pt] font-extrabold flex py-0">
                                <span>27</span>
                            </div>
                            <div class="">
                                <div class="lg:font-semibold leading-none lg:text-3xl">years in</div>
                                <div class="lg:font-semibold leading-none lg:text-2xl">the industry</div>
                            </div>
                        </div>
                    </div>

                    <div class="shadow-lg bg-white lg:col-span-7 lg:row-span-2 col-span-3 row-span-3 p-5 lg:p-10 text-left overflow-hidden relative">
                        <div class="rounded-full bg-blue-200 absolute -top-10 -right-10 h-60 w-60 blur-xl"></div>
                        <div class="rounded-full bg-green-100 absolute -bottom-20 -left-10 h-60 w-60 blur-xl"></div>

                        <div class="relative">
                            <p class="lg:text-xl font-light">
                                Backed by a team of professionals with diverse expertise, FCU created OnePage to embody its mission of driving positive organizational change through innovative, practical solutions.
                            </p><br>
                            <p class="lg:text-xl font-light">
                                Built on a legacy of numerous client partnerships, OnePage reflects our commitment to simplifying ISO compliance while empowering businesses to grow and succeed.
                            </p>
                        </div>
                    </div>

                    <div class="shadow-lg bg-white lg:col-span-2 col-span-3 row-span-1 lg:block hidden lg:row-span-2 overflow-hidden">
                        <img src="{{ asset('img/team-hands.png') }}" alt="team hands" class="w-full h-full object-cover">
                    </div>

                    <div class="shadow-lg bg-white lg:col-span-3 flex justify-center items-center lg:min-h-34 col-span-3">
                        <img src="{{ asset('img/fcu-logo.jpg') }}" alt="fcu-logo">
                    </div>

                    <div class="shadow-lg bg-gradient-to-r from-blue-500 to-green-500 lg:col-span-10 py-3 flex flex-col items-center min-h-36 lg:h-36 row-span-2 col-span-3">
                        <div class="rounded-full border border-2 border-blue-800 text-blue-500 font-bold lg:text-lg px-3 bg-blue-100 lg:w-50 leading-tight">OUR VALUES</div>
                        <div class="grid grid-cols-2 lg:grid-cols-5 h-full text-white mt-1">
                            <div class="leading-none flex flex-col justify-center items-center">
                                <span>Respect</span>
                                <img src="{{asset('img/respect.png')}}" alt="respect" class="h-10 lg:h-15">
                            </div>
                            <div class="leading-none flex flex-col lg:flex-col-reverse justify-center items-center">
                                <span>Commitment <br>& Passion</span>
                                <img src="{{asset('img/commitment.png')}}" alt="commitment" class="h-10 lg:h-15">
                            </div>
                            <div class="leading-none flex flex-col justify-center items-center">
                                <span>Integrity</span>
                                <img src="{{asset('img/integrity.png')}}" alt="integrity" class="h-10 lg:h-15">
                            </div>
                            <div class="leading-none flex flex-col lg:flex-col-reverse justify-center items-center">
                                <span>Personal Growth & Learning</span>
                                <img src="{{asset('img/growth.png')}}" alt="growth" class="h-10 lg:h-15">
                            </div>
                            <div class="leading-none flex flex-col justify-center items-center">
                                <span>Excellence</span>
                                <img src="{{asset('img/excellence.png')}}" alt="excellence" class="h-10 lg:h-15">
                            </div>
                        </div>
                    </div>

                    <div class="shadow-lg bg-white lg:col-span-2 col-span-3 flex flex-row gap-3 lg:flex-col lg:gap-0 justify-center items-center rounded-br-[3rem] p-4 gap-0 min-h-36 lg:h-36">
                        <i class="fa-solid fa-users text-[28pt] leading-none"></i>
                        <div>
                            <div class="text-[30pt] font-extrabold leading-none">400+</div>
                            <div class="text-[12pt] font-semibold leading-tight">Clients</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="contact-section" class="bg-blue-600 px-20 py-10">
            <div class="flex text-white w-full font-extralight">
                <div class="w-2/5 border-r flex-col flex justify-between">
                    <div class="flex text-gray-100 font-bold text-3xl gap-2 items-center">
                        <img src="{{ asset('onepage-blue.png') }}" alt="OnePage Logo" class="w-12" />
                        <div>OnePage</div>
                    </div>
                    <p class="lg:w-60">A powerful, easy-to-use platform that centralizes your documents and speeds up ISO readiness.</p>
                </div>
                <div class="w-3/5 text-right">
                    <div class="font-normal">Contact Us</div>
                    <div>
                        02-8332-0264 <br>
                        <a href="https://www.fcusolutions.org/" target="_blank">Visit our Website</a><br><br>
                        8R FUTURE POINT PLAZA 2 <br>
                        MO. IGNACIA ST., QUEZON CITY
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-landing-layout>