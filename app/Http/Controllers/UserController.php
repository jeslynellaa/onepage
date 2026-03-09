<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $users = \App\Models\User::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('middle_name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id','first_name','middle_name','last_name']);
        return response()->json($users);
    }

    public function profile(User $user)
    {
        return view('profile.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'username' => ['required', Rule::unique('users', 'username')->ignore(auth()->id())],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(auth()->id())],
            'signature' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ]);

        // Update basic fields
        $user->update([
            'first_name'  => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name'   => $validated['last_name'],
            'username'    => $validated['username'],
            'email'       => $validated['email'],
        ]);

        if ($request->hasFile('signature'))
        {
            if ($user->signature_path) {
                Storage::disk('public')->delete($user->signature_path);
            }

            $path = $request->file('signature')->store(
                'signatures',
                'public'
            );

            $user->update([
                'signature_path' => $path
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated.');
    }

    public function updatePassword(User $user, Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password is incorrect.',
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}

