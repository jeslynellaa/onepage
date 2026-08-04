<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\ClientUser;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'sections' => 'nullable|array',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.description' => 'nullable|string|max:20',
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

        $sectionNumber = 1;
        foreach ($validated['sections'] ?? [] as $section) {
            if (trim($section['title'] ?? '') === '') {
                continue;
            }

            Section::create([
                'company_id' => $company->id,
                'manual' => 'System Procedures',
                'section_number' => str_pad($sectionNumber, 2, '0', STR_PAD_LEFT),
                'title' => $section['title'],
                'description' => $section['description'] ?? null,
            ]);

            $sectionNumber++;
        }

        return redirect()->back()->with('success', 'Company client has been successfully created.');
    }

    public function view(Company $client)
    {
        $userAccounts = User::where('company_id', $client->id)->get();
        $invitations = Invitation::where('company_id', $client->id)->get();
        $processes = Section::where('company_id', $client->id)->get();
        $consultantAssignments = ClientUser::where('company_id', $client->id)
            ->active()
            ->with(['user', 'assignedBy'])
            ->get();
        $availableConsultants = User::where('company_id', 1)->get();

        return view('clients.view', compact('client', 'userAccounts', 'invitations', 'processes', 'consultantAssignments', 'availableConsultants'));
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

    public function storeSection(Request $request, Company $client)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:20',
        ]);

        $nextNumber = Section::where('company_id', $client->id)->count() + 1;

        Section::create([
            'company_id' => $client->id,
            'manual' => 'System Procedures',
            'section_number' => str_pad($nextNumber, 2, '0', STR_PAD_LEFT),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Process name added successfully.');
    }

    public function updateSection(Request $request, Company $client, Section $section)
    {
        abort_unless($section->company_id === $client->id, 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:20',
        ]);

        $section->update($validated);

        return redirect()->back()->with('success', 'Process name updated successfully.');
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
            'company_id' => $client->id,
        ]);

        // 3. Optionally: send email invite here
        // Mail::to($invitation->email)->send(new InvitationMail($invitation));

        // 4. Redirect back with success message
        return redirect()->back()->with('success', 'Invitation saved successfully!');
    }

    public function assignConsultant(Company $client, Request $request)
    {
        if (! Gate::allows('enter-admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $consultant = User::where('id', $validated['user_id'])
            ->where('company_id', 1)
            ->firstOrFail();

        $existing = ClientUser::where('user_id', $consultant->id)
            ->where('company_id', $client->id)
            ->first();

        if ($existing) {
            $existing->update([
                'status' => 'active',
                'revoked_at' => null,
                'assigned_by_user_id' => auth()->id(),
            ]);
        } else {
            ClientUser::create([
                'user_id' => $consultant->id,
                'company_id' => $client->id,
                'status' => 'active',
                'assigned_by_user_id' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Consultant assigned to client.');
    }

    public function revokeConsultant(ClientUser $clientUser)
    {
        if (! Gate::allows('enter-admin')) {
            abort(403);
        }

        $clientUser->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Consultant access revoked.');
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
