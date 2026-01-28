<?php

namespace App\Http\Controllers;

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
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required',
            'subscription_plan' => 'required',
            'subscription_status' => 'required',
            'logo' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'hex_code' => '',
        ]);
        
        $slug = Str::slug($validated['name']);

        if ($request->hasFile('logo'))
        {
            $path = $request->file('logo')->store(
                'client_logos',
                'public'
            );
        }

        $company = Company::create([
            'name'  => $validated['name'],
            'slug' => $slug,
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_status' => $validated['subscription_status'],
            'subscription_ends_at' => null,
            'logo_path' => $path ?? null,
            'hex_code' => $validated['hex_code'],
        ]);

        return redirect()->back()->with('success', 'Company client has been successfully created.');
    }
}
