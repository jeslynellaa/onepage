<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
        return view('clients.view', compact('client', 'userAccounts'));
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
}
