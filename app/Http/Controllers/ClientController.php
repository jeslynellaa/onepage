<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;

class ClientController extends Controller
{
    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'subscription_plan' => 'required',
            'subscription_status' => 'required',
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'hex_code' => '',
        ]);

        if ($request->hasFile('logo'))
        {
            $path = $request->file('logo')->store(
                'client_logos',
                'public'
            );
        }

        $company = Company::create([
            'name'  => $validated['name'],
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_status' => $validated['subscription_status'],
            'subscription_ends_at' => null,
            'logo_path' => $path ?? null,
            'hex_code' => $validated['hex_code'],
        ]);

        return redirect()->back()->with('success', 'Company client has been successfully created.');
    }

    public function view(Company $client)
    {
        $userAccounts = User::where('company_id', $client->id)->get();
        $invitations = Invitation::where('company_id', $client->id)->get();

        return view('clients.view', compact('client', 'userAccounts', 'invitations'));
    }

    public function edit(Company $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Company $client)
    {
        $validated = $request->validate([
            'name' => 'required',
            'subscription_plan' => 'required',
            'subscription_status' => 'required',
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'hex_code' => '',
        ]);
        
        // Update basic fields
        $client->update([
            'name' => $validated['name'],
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_status' => $validated['subscription_status'],
            'hex_code' => $validated['hex_code'],
        ]);

        if ($request->hasFile('logo'))
        {
            if ($client->logo_path) {
                Storage::disk('public')->delete($client->logo_path);
            }

            $path = $request->file('logo')->store(
                'client_logos',
                'public'
            );

            $client->update([
                'logo_path' => $path
            ]);
        }

        return redirect()->back()->with('success', 'Company client data has been successfully updated.');
    }

    public function invite(Company $client, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:invitations,email',
            'role' => 'required|string',
        ]);
        
        $token = hash('sha256', Str::random(60));

        $invitation = Invitation::create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => $token,
            'sent_out' => false,
            'expires_at' => now()->addDays(7),
        ]);

        // 3. Optionally: send email invite here
        // Mail::to($invitation->email)->send(new InvitationMail($invitation));

        // 4. Redirect back with success message
        return redirect()->back()->with('success', 'Invitation saved successfully!');
    }

    public function send(Invitation $invitation)
    {
        $invitation->load('company');
        if ($invitation->sent_out) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation already sent.'
            ], 422);
        }
        
        try {
            Mail::to($invitation->email)->send(new InvitationMail($invitation));

            $invitation->update([
                'sent_out' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invitation sent.'
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation. '. $e
            ], 500);
        }
    }
}
