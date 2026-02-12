<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function welcome()
    {
        return view('landing.index');
    }

    public function index() {
        $activeCount = Document::where('status', "Active")->count();
        $draftCount = Document::where('status', "Draft")->count();
        $reviewCount = Document::where('status', "For Review")->count();
        $approvalCount = Document::where('status', "For Approval")->count();
        // $logs = ActivityLog::latest('performed_at')->get();
        $logs = ActivityLog::latest('performed_at')
            ->take(20)
            ->get();

        $forReview = Document::where('status', "For Review")->get();

        $forApproval = Document::where('status', "For Review")->get();
        
        return view('index', compact('activeCount', 'draftCount', 'reviewCount', 'approvalCount', 'logs', 'forReview', 'forApproval'));
    }

    public function showLogs()
    {
        $logs = ActivityLog::orderBy('performed_at')->get();
        // dd($logs);
        return view('activity.index', compact('logs'));
    }
}
