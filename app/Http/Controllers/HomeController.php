<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $pendingCode = collect();
        if(auth()->user()->role == 'Document Controller'){
            $pendingCode = Document::where('status', 'Pending Code')
            ->get();
        }
        $allActions = collect()
        ->concat($pendingReviews)
        ->concat($pendingApprovals)
        ->concat($pendingCode)
        ->sortByDesc('updated_at');

        $allLoggedDurations = ActivityLog::query()
            ->select('document_id', 'action', 'created_at')
            ->whereIn('action', ['created draft', 'submitted for review', 'review passed', 'approved', 'assigned code'])
            ->orderBy('document_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('document_id')
            ->flatMap(function ($logs) {
                $flatList = [];
                $logs = $logs->values(); 

                foreach ($logs as $key => $log) {
                    $nextLog = $logs->get($key + 1);

                    if (!$nextLog && $log->action === 'assigned code') {
                        continue; 
                    }

                    $endTime = $nextLog ? $nextLog->created_at : now();
                    $diffInHours = $log->created_at->diffInMinutes($endTime) / 60;

                    $flatList[] = (object) [
                        'action_name' => $log->action,
                        'duration'    => $diffInHours
                    ];
                }
                return $flatList;
            });

        $processStats = $allLoggedDurations->groupBy('action_name')
            ->map(function ($group, $action) {
                return [
                    'action'  => $action,
                    'average' => round($group->avg('duration'), 2),
                    'count'   => $group->count() // This will now show 10, 20, 50, etc.
                ];
            })
            ->values();

        // dd($processStats);
        return view('index', compact('activeCount', 'draftCount', 'reviewCount', 'approvalCount', 'logs', 'allActions', 'processStats'));
    }

    public function showLogs()
    {
        $logs = ActivityLog::orderBy('performed_at')->get();
        // dd($logs);
        return view('activity.index', compact('logs'));
    }
}
