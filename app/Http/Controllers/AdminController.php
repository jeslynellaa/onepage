<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function index ()
    {
        if (! Gate::allows('enter-admin')) {
            abort(403);
        }
        // Demo workspaces aren't clients; they're listed on the Demos page.
        $clients = Company::where('is_demo', false)->get();
        $clientCount = $clients->count();
        $openDemoCount = Company::where('is_demo', true)->where('demo_expires_at', '>', now())->count();
        return view('admin.index', compact('clients', 'clientCount', 'openDemoCount'));
    }
}
