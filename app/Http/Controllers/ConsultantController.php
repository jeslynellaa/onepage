<?php

namespace App\Http\Controllers;

use App\Models\ClientUser;
use App\Models\Company;
use Illuminate\Support\Facades\Gate;

class ConsultantController extends Controller
{
    public function index()
    {
        if (! Gate::allows('consultant-access')) {
            abort(403);
        }

        $assignments = ClientUser::where('user_id', auth()->id())
            ->active()
            ->with('company')
            ->get();

        return view('consultant.clients', compact('assignments'));
    }

    public function enter(Company $client)
    {
        if (! Gate::allows('consultant-access')) {
            abort(403);
        }

        $assigned = ClientUser::where('user_id', auth()->id())
            ->where('company_id', $client->id)
            ->active()
            ->exists();

        if (! $assigned) {
            abort(403, 'You are not assigned to this client.');
        }

        session(['active_client_id' => $client->id]);

        return redirect()->route('dashboard')->with('success', "Now working in {$client->name}.");
    }

    public function exit()
    {
        session()->forget('active_client_id');

        return redirect()->route('dashboard')->with('success', 'Exited client mode.');
    }
}
