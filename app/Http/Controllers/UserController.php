<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'middle_name' => ['required'],
            'last_name' => ['required'],
            'username' => ['required'],
            'signature' => ['nullable', 'image', 'mimes:png', 'max:2048'],
        ]);

        // Update basic fields
        $user->update([
            'first_name'  => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name'   => $validated['last_name'],
            'username'    => $validated['username'],
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
            // dd($path);
        }

        return redirect()->back()->with('success', 'Profile updated.');
    }
}

