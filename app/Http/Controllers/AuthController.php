<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister(Request $request)
    {
        $token = $request->query('token');

        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation || $invitation->isUsed() || $invitation->isExpired()) {
            abort(403, "This registration link is invalid, already used, or expired. Contact your company's admin for help.");
        }

        $company = Company::find($invitation->company_id);

        return view('auth.register', ['invitation' => $invitation], compact('company'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|unique:users|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|string|max:255',
            'company_id' => 'required',
        ]);

        // Find the invitation
        $invitation = Invitation::where('token', $request->token)->first();

        // Check if the invitation is already used or expired
        if ($invitation->isUsed() || $invitation->isExpired()) {
            return back()->withErrors(['token' => 'This registration link is invalid, already used, or expired.']);
        }

        // Encrypt the password with hash
        $validated['password'] = Hash::make($request->password);

        $user = User::create(array_merge($validated, [
            'active' => true
        ]));

        // Mark invitation as used
        $invitation->used_at = now();
        $invitation->save();

        Auth::login($user);
        return redirect('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}