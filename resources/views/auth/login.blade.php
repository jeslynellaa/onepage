<x-layout>
    <div class="flex justify-center bg-gray-100">
        <form method="POST" action="/login" class="bg-white p-6 rounded-2xl shadow-md w-100">
            @csrf
            <h2 class="text-2xl font-semibold mb-4 text-center">Login</h2>
            @error('email')
                <div class="text-red-600 text-sm mb-2">{{ $message }}</div>
            @enderror
            <input type="email" name="email" placeholder="Email" class="w-full border rounded px-3 py-2 mb-3" required>
            <input type="password" name="password" placeholder="Password" class="w-full border rounded px-3 py-2 mb-4" required>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded">Login</button>
            <div class="text-sm text-center mt-3">
            <a href="/register" class="text-blue-500 hover:underline">Create an account</a>
            </div>
        </form>
    </div>
</x-layout>