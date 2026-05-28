<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookDemoController extends Controller
{
    public function index()
    {
        return view('landing.book_demo');
    }

    public function store(Request $request)
    {
        // The exact validation array you have stays the same
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'required|string|max:255',
            'teamSize' => 'required|string',
            'demo_time' => 'required|string',
            'demo_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->addDays(2)->toDateString(),
                function ($attribute, $value, $fail) {
                    $carbonDate = \Carbon\Carbon::parse($value);
                    if ($carbonDate->isSunday()) {
                        $fail('Demos cannot be scheduled on Sundays.');
                    }
                    if ($carbonDate->isMonday() && $carbonDate->day <= 7) {
                        $fail('Sorry, date selected is not available.');
                    }
                },
            ],
        ]);

        try {
            // Send the email
            \Illuminate\Support\Facades\Mail::to('onepagefcu@gmail.com')->send(new \App\Mail\DemoRequestedMail($validated));

            // Redirect back with a standard flash message!
            return redirect()->back()->with('success', 'Demo request submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['mail_error' => 'Something went wrong. Please try again later.']);
        }
    }
}
