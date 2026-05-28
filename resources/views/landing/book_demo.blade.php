<x-landing-layout>
    <div class="mx-auto set-x-padding bg-gradient-to-b from-gray-100 to-blue-300 min-h-screen py-2">
        <div class="max-w-md mx-auto text-center mb-4">
            <h2 class="text-3xl font-extrabold text-gray-900">Book a Free Demo</h2>
            <p class="mt-3 text-sm text-gray-600">See how our platform can streamline your document workflows and team collaboration.</p>
        </div>

        <div class="max-w-md mx-auto w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <form method="POST" action="{{ route('demo.store') }}" class="space-y-5">@csrf
                @if ($errors->any())
                    <div class="bg-red-50 text-red-700 p-4 rounded-xl text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="text-center py-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Demo Request Received!</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ session('success') }}</p>
                    </div>
                @endif
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors" placeholder="Juan dela Cruz">
                </div>

                <!-- Work Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Work Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors" placeholder="juan@company.com">
                </div>

                <!-- Company Name -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <input type="text" id="company" name="company" value="{{ old('company') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors" placeholder="ACME Corp.">
                </div>

                <!-- Estimated Team Size / Users -->
                <div>
                    <label for="team_size" class="block text-sm font-medium text-gray-700 mb-1">Estimated Users Needed</label>
                    <select id="teamSize" name="teamSize" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors">
                        <option value="1-15" {{ old('teamSize') == '1-15' ? 'selected' : '' }}>1 - 15 users (Standard Plan)</option>
                        <option value="15+" {{ old('teamSize') == '31+' ? 'selected' : '' }}>More than 15 users</option>
                    </select>
                </div>

                <!-- Preferred Date & Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="demo_date" class="block text-sm font-medium text-gray-700 mb-1">Preferred Date</label>
                        <input type="date" id="demo_date" name="demo_date" value="{{ old('date') }}" required class="selector w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors" placeholder="Select Preffered Date">
                    </div>
                    <div>
                        <label for="demo_time" class="block text-sm font-medium text-gray-700 mb-1">Preferred Time</label>
                        <select id="demo_time" name="demo_time" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors">
                            <option value="morning" {{ old('time') == 'morning' ? 'selected' : '' }}>Morning (9:00 AM - 12:00 PM)</option>
                            <option value="afternoon" {{ old('time') == 'afternoon' ? 'selected' : '' }}>Afternoon (1:00 PM - 5:00 PM)</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center items-center px-5 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-200">Schedule My Demo</button>
                </div>
            </form>

            <!-- Success State Message (Hidden by default) -->
            <div id="successMessage" class="hidden text-center py-8">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Demo Request Received!</h3>
                <p class="mt-2 text-sm text-gray-600">Thank you. We will send a calendar invite and meeting link to your work email shortly.</p>
            </div>
        </div>
    </div>
    <x-slot:scripts>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            const twoDaysFromNow = new Date();
            twoDaysFromNow.setDate(twoDaysFromNow.getDate() + 7);
            
            $(".selector").flatpickr({
                'minDate': twoDaysFromNow,
                'disable': [
                    function(date) {
                        const dayOfWeek = date.getDay();   // 0 = Sunday, 1 = Monday, etc.
                        const dayOfMonth = date.getDate(); // 1 to 31

                        // 1. Disable Sundays
                        if (dayOfWeek === 0) {
                            return true;
                        }

                        // 2. Disable the first Monday of the month
                        if (dayOfWeek === 1 && dayOfMonth <= 7) {
                            return true;
                        }

                        return false; // Leave all other days enabled
                    }
                ]
            });
        </script>
    </x-slot:scripts>
</x-landing-layout>