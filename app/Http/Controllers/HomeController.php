<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\MsManual;
use App\Models\SupportDocument;
use App\Models\Form;
use App\Models\ActivityLog;
use Carbon\Carbon;

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

    public function pricing()
    {
        return view('landing.pricing');
    }

    public function oldIndex() {
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
            ->where('document_type', 'system_procedure')
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
                    'average' => 6,
                    'count'   => $group->count() // This will now show 10, 20, 50, etc.
                ];
            })
            ->values();

        // dd($processStats);
        return view('index', compact('activeCount', 'draftCount', 'reviewCount', 'approvalCount', 'logs', 'allActions', 'processStats'));
    }

    public function index() {
        // Count across all four controlled-document types (System Procedures,
        // MS Manuals, Support Documents, Forms), not just System Procedures.
        $documentTypeModels = [Document::class, MsManual::class, SupportDocument::class, Form::class];
        $countAcrossTypes = fn (string $status) => collect($documentTypeModels)
            ->sum(fn (string $model) => $model::where('status', $status)->count());

        $activeCount = $countAcrossTypes('Active');
        $draftCount = $countAcrossTypes('Draft');
        $reviewCount = $countAcrossTypes('For Review');
        $approvalCount = $countAcrossTypes('For Approval');
        $logs = ActivityLog::latest('performed_at')
            ->take(20)
            ->get();

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
        if (auth()->user()->role == 'Document Controller') {
            $pendingCode = Document::where('status', 'Pending Code')->get();
        }
        $allActions = collect()
            ->concat($pendingReviews)
            ->concat($pendingApprovals)
            ->concat($pendingCode)
            ->sortByDesc('updated_at');

        // Average end-to-end cycle time (draft -> assigned code) for documents
        // that have completed the full workflow, so this mirrors the
        // "Ave. Document Cycle" figure advertised on the redesigned landing page.
        $completedCycles = ActivityLog::query()
            ->where('document_type', 'system_procedure')
            ->select('document_id', 'action', 'created_at')
            ->whereIn('action', ['created draft', 'submitted for review', 'review passed', 'approved', 'assigned code'])
            ->orderBy('document_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('document_id')
            ->map(function ($logs) {
                $logs = $logs->values();
                $first = $logs->first();
                $last = $logs->last();

                if ($last->action !== 'assigned code') {
                    return null;
                }

                return $first->created_at->diffInMinutes($last->created_at) / 60;
            })
            ->filter(fn ($hours) => $hours !== null);

        $avgCycleHours = $completedCycles->isNotEmpty() ? $completedCycles->avg() : null;

        $totalDocuments = collect($documentTypeModels)->sum(fn (string $model) => $model::count());
        $codedThisMonth = ActivityLog::where('document_type', 'system_procedure')
            ->where('action', 'assigned code')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        // "Open Actions" ring — live share of in-flight documents by stage,
        // drawn as an SVG ring to match the landing page's process visual
        // (see resources/views/landing/index.blade.php, "Plate B").
        $inFlightTotal = $draftCount + $reviewCount + $approvalCount;
        $circumference = 238.76; // 2 * pi * r(38)

        $draftShare = $inFlightTotal > 0 ? $draftCount / $inFlightTotal : 0;
        $reviewShare = $inFlightTotal > 0 ? $reviewCount / $inFlightTotal : 0;
        $approvalShare = $inFlightTotal > 0 ? $approvalCount / $inFlightTotal : 0;

        $draftArcLength = $draftShare * $circumference;
        $reviewArcLength = $reviewShare * $circumference;
        $approvalArcLength = $approvalShare * $circumference;

        $reviewArcOffset = -$draftArcLength;
        $approvalArcOffset = -($draftArcLength + $reviewArcLength);

        $draftSharePct = round($draftShare * 100);
        $reviewSharePct = round($reviewShare * 100);
        $approvalSharePct = round($approvalShare * 100);

        // "Document Lifecycle" ring — average time spent in each pre-Active stage
        // (Draft, Review, Approval, waiting on a code), not a snapshot of current
        // status counts. Active is deliberately excluded: it's the terminal state
        // a document sits in indefinitely, not a stage duration, so it would
        // always dominate the chart and say nothing about the process itself.
        $stageGaps = ActivityLog::query()
            ->where('document_type', 'system_procedure')
            ->select('document_id', 'action', 'created_at')
            ->whereIn('action', ['created draft', 'submitted for review', 'review passed', 'approved', 'assigned code'])
            ->orderBy('document_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('document_id')
            ->flatMap(function ($logs) {
                $logs = $logs->values();
                $gaps = [];

                foreach ($logs as $key => $log) {
                    $nextLog = $logs->get($key + 1);

                    if (!$nextLog) {
                        continue;
                    }

                    $gaps[] = [
                        'stage' => $log->action,
                        'hours' => $log->created_at->diffInMinutes($nextLog->created_at) / 60,
                    ];
                }

                return $gaps;
            });

        $stageStartAction = [
            'Draft' => 'created draft',
            'Review' => 'submitted for review',
            'Approval' => 'review passed',
            'Pending Code' => 'approved',
        ];

        $stageAvgHours = [];
        foreach ($stageStartAction as $label => $action) {
            $hours = $stageGaps->where('stage', $action)->pluck('hours');
            $stageAvgHours[$label] = $hours->isNotEmpty() ? $hours->avg() : 0;
        }

        $draftAvgHours = $stageAvgHours['Draft'];
        $reviewAvgHours = $stageAvgHours['Review'];
        $approvalAvgHours = $stageAvgHours['Approval'];
        $pendingCodeAvgHours = $stageAvgHours['Pending Code'];

        $totalLifecycleHours = $draftAvgHours + $reviewAvgHours + $approvalAvgHours + $pendingCodeAvgHours;
        $totalLifecycleDays = $totalLifecycleHours / 24;

        $draftLifecycleShare = $totalLifecycleHours > 0 ? $draftAvgHours / $totalLifecycleHours : 0;
        $reviewLifecycleShare = $totalLifecycleHours > 0 ? $reviewAvgHours / $totalLifecycleHours : 0;
        $approvalLifecycleShare = $totalLifecycleHours > 0 ? $approvalAvgHours / $totalLifecycleHours : 0;
        $pendingCodeLifecycleShare = $totalLifecycleHours > 0 ? $pendingCodeAvgHours / $totalLifecycleHours : 0;

        $draftLifecycleArcLength = $draftLifecycleShare * $circumference;
        $reviewLifecycleArcLength = $reviewLifecycleShare * $circumference;
        $approvalLifecycleArcLength = $approvalLifecycleShare * $circumference;
        $pendingCodeLifecycleArcLength = $pendingCodeLifecycleShare * $circumference;

        $reviewLifecycleArcOffset = -$draftLifecycleArcLength;
        $approvalLifecycleArcOffset = -($draftLifecycleArcLength + $reviewLifecycleArcLength);
        $pendingCodeLifecycleArcOffset = -($draftLifecycleArcLength + $reviewLifecycleArcLength + $approvalLifecycleArcLength);

        $draftLifecyclePct = round($draftLifecycleShare * 100);
        $reviewLifecyclePct = round($reviewLifecycleShare * 100);
        $approvalLifecyclePct = round($approvalLifecycleShare * 100);
        $pendingCodeLifecyclePct = round($pendingCodeLifecycleShare * 100);

        return view('temp_index', compact(
            'activeCount', 'draftCount', 'reviewCount', 'approvalCount',
            'logs', 'allActions', 'pendingReviews', 'pendingApprovals', 'avgCycleHours',
            'totalDocuments', 'codedThisMonth', 'inFlightTotal', 'circumference',
            'draftArcLength', 'reviewArcLength', 'approvalArcLength',
            'reviewArcOffset', 'approvalArcOffset',
            'draftSharePct', 'reviewSharePct', 'approvalSharePct',
            'totalLifecycleHours', 'totalLifecycleDays',
            'draftLifecycleArcLength', 'reviewLifecycleArcLength', 'approvalLifecycleArcLength', 'pendingCodeLifecycleArcLength',
            'reviewLifecycleArcOffset', 'approvalLifecycleArcOffset', 'pendingCodeLifecycleArcOffset',
            'draftLifecyclePct', 'reviewLifecyclePct', 'approvalLifecyclePct', 'pendingCodeLifecyclePct'
        ));
    }

    public function showLogs()
    {
        $logs = ActivityLog::latest('performed_at')->get();
        // dd($logs);
        return view('activity.index', compact('logs'));
    }
}
