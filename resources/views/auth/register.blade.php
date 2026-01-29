<x-layout>
    <div class="flex justify-center bg-gray-100">
        <form method="POST" action="/register" class="bg-white p-6 rounded-2xl shadow-md w-2/4">
            @csrf
            <h2 class="text-2xl font-semibold mb-4 text-center">Register</h2>
            <input type="hidden" name="token" value="{{ request('token') }}">
            <input type="text" name="first_name" placeholder="Given Name" class="w-full border rounded px-3 py-2 mb-3" required>

            <div class="flex gap-3">
                <input type="text" name="middle_name" placeholder="Middle Name" class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3 border rounded" required>
                <input type="text" name="last_name" placeholder="Last Name" class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3 border rounded" required>
            </div>

            <div class="flex gap-3">
                <input type="text" name="username" placeholder="User Name" class="w-full sm:w-1/2 md:w-1/3 px-2 mb-3 border rounded" required>
                <input type="email" name="email" placeholder="Email" value="{{ $invitation->email }}" class="w-full border rounded px-3 py-2 mb-3" required>
            </div>

            <input type="password" name="password" placeholder="Password" class="w-full border rounded px-3 py-2 mb-3" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full border rounded px-3 py-2 mb-4" required>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded">Register</button>
            <div class="text-sm text-center mt-3">
                <a href="/login" class="text-blue-500 hover:underline">Already have an account?</a>
            </div>
        </form>
    </div>
</x-layout>