<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ActivityLog;

class HomeController extends Controller
{
    public function welcome()
    {
        return view('landing.index');
    }

    public function faqs()
    {
        return view('landing.faqs');
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
    
        $userId = auth()->id();
        $pendingReviews = Document::where('status', 'For Review')
            ->whereHas('section', function ($query) {
                $query->where('reviewer_id', auth()->id());
            })
            ->get();
        $pendingApprovals = Document::where('status', 'For Approval')
            ->whereHas('section', function ($query) {
                $query->where('approver_id', auth()->id());
            })
            ->get();
        return view('index', compact('activeCount', 'draftCount', 'reviewCount', 'approvalCount', 'logs', 'pendingReviews', 'pendingApprovals'));
    }

    public function showLogs()
    {
        $logs = ActivityLog::orderBy('performed_at')->get();
        // dd($logs);
        return view('activity.index', compact('logs'));
    }
}
