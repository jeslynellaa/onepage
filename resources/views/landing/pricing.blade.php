<x-landing-layout>
    <div class="mx-auto set-x-padding bg-gradient-to-b from-gray-100 to-blue-300 h-full">
        <div class="mt-16 text-center mb-4">
            <h1 class="text-4xl font-black">Pricing Offers</h1>
        </div>

            <!-- Pricing Card Container -->
            <div class="max-w-sm mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-8 pt-8 pb-6 text-center bg-gray-50 border-b border-gray-100">
                    <span class="inline-flex px-4 py-1 rounded-full text-xs font-semibold tracking-wide uppercase bg-indigo-100 text-indigo-800">Standard Plan</span>
                    
                    <!-- Price Display (Updated via JS/Alpine) -->
                    <div class="mt-4 flex items-baseline justify-center text-gray-900">
                        <span class="text-2xl font-semibold">Php</span>
                        <span id="plan-price" class="text-5xl font-extrabold tracking-tight">2,480</span>
                        <span id="price-period" class="ml-1 text-xl font-medium text-gray-500">/mo</span>
                    </div>
                    
                    <!-- Plan Duration Selector -->
                    <div class="mt-6 inline-flex p-1 bg-gray-200/80 rounded-lg">
                        <button type="button" onclick="updatePrice('monthly', 2480, '/mo')" id="btn-monthly" class="px-3 py-1.5 text-xs font-medium rounded-md bg-white text-gray-900 shadow-sm transition-all">1 Month</button>
                        <button type="button" onclick="updatePrice('yearly', 28800, '/yr')" id="btn-yearly" class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:text-gray-900 transition-all">1 Year</button>
                        <button type="button" onclick="updatePrice('triennial', 80880, '/3 yrs')" id="btn-triennial" class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:text-gray-900 transition-all">3 Years</button>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="px-8 pt-6 pb-8 space-y-6">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <p class="ml-3 text-sm text-gray-700 font-medium">Up to 15 users included</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <p class="ml-3 text-sm text-gray-700">Includes all features</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <p class="ml-3 text-sm text-gray-700">Accessible across all devices</p>
                        </li>
                    </ul>

                    <!-- Add-on Notice -->
                    <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100 text-center">
                        <p class="text-xs text-indigo-900 font-semibold uppercase tracking-wider">Need more team members?</p>
                        <p class="mt-1 text-sm text-gray-600">Add extra users for just <span class="font-bold text-indigo-700">Php 158</span> per user / month.</p>
                    </div>

                    <!-- CTA Button -->
                    <a href="/book-demo" class="w-full flex justify-center items-center px-5 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-200">Get Started</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function updatePrice(period, amount, label) {
            // Update the price text and period label
            document.getElementById('plan-price').innerText = amount.toLocaleString();
            document.getElementById('price-period').innerText = label;

            // Reset button styles
            const buttons = ['btn-monthly', 'btn-yearly', 'btn-triennial'];
            buttons.forEach(id => {
                const btn = document.getElementById(id);
                btn.className = "px-3 py-1.5 text-xs font-medium rounded-md text-gray-700 hover:text-gray-900 transition-all";
            });

            // Apply active styles to the selected button
            const activeBtn = document.getElementById(`btn-${period}`);
            activeBtn.className = "px-3 py-1.5 text-xs font-medium rounded-md bg-white text-gray-900 shadow-sm transition-all";
        }
    </script>
</x-landing-layout>