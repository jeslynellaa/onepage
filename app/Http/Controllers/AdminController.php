<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index ()
    {
        $clientCount = Company::count();
        $clients = Company::all();
        return view('admin.index', compact('clients', 'clientCount'));
    }
}
