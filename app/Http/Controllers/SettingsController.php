<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        
        return view('settings.index', compact('company'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'brand_color'  => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'paper_size'   => 'required|in:a4,letter,legal',
            'pdf_font'     => 'required|in:Helvetica,Times-Roman,Courier',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        $company = Company::firstOrFail();

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }

            // Store new logo in 'logos' folder within public disk
            $path = $request->file('logo')->store('client_logos', 'public');
            $company->logo_path = $path;
        }

        // 4. Update the rest of the fields
        $company->name = $validated['company_name'];
        $company->hex_code = $validated['brand_color'];
        $company->paper_size = $validated['paper_size'];
        $company->pdf_font = $validated['pdf_font'];
        
        $company->save();

        return back()->with('success', 'System settings updated successfully.');
    }
}
