<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FormsManualController extends Controller
{
    public function index() {
        $documents = Form::all();

        $sections = Section::where('company_id', \App\Support\CompanyContext::id())->with(['processOwner', 'reviewer', 'approver'])->orderBy('section_number')->get();
        $users = User::where('company_id', \App\Support\CompanyContext::id())
            ->orderBy('last_name', 'ASC')
            ->get(['id', 'first_name', 'middle_name', 'last_name']);
        $user_list = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->last_name . ', ' . $user->given_name . ' ' . $user->middle_name
            ];
        })->toArray();
        $totalCount = 0;
        foreach($sections as $section){
            $sectionCount = $documents->where('section_id', $section->id)
                ->sortByDesc('created_at')   // or revision_number
                ->unique('code')
                ->count();
            $section->count = $sectionCount;
            $totalCount += $sectionCount;
        }
        return view('document.forms_manual.index', compact('documents', 'sections', 'totalCount', 'user_list'));
    }


    public function getFormsSectionDocuments(Request $request)
    {
        $sectionId = $request->input('sectionId');

        $items = Form::where('section_id', $sectionId)
            ->orderBy('code')
            ->orderByDesc('created_at')
            ->get()
            ->unique('code')
            ->values();

        return response()->json([
            'csrf' => csrf_token(),
            'items' => $items->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'code' => $doc->code,
                    'pages' => $doc->pages,
                    'status' => $doc->status,
                    'revision_number' => $doc->revision_number,
                    'effective_date' => $doc->effective_date,

                    // URLs
                    'viewUrl' => route('document.forms.view', $doc->id),
                    'editUrl' => route('document.forms.edit', $doc->id),
                    'deleteUrl' => route('document.forms.destroy', $doc->id),
                    'revHistoryUrl' => route('document.forms.rev_history', $doc->code),
                    'sendForReviewUrl' => route('document.forms.forReview', $doc->id),
                    'reviewDecisionUrl' => route('document.forms.reviewPassOrFail', $doc->id),
                    'approveDecisionUrl' => route('document.forms.approveOrNot', $doc->id),
                    'assignCodeUrl' => route('document.forms.assignCode', $doc->id),

                    // 🔐 AUTH FLAGS (Policy-based)
                    'can' => [
                        'edit' => auth()->user()->can('update', $doc),
                        'delete' => auth()->user()->can('delete', $doc),
                        'send' => auth()->user()->can('sendForReview', $doc),
                        'review' => auth()->user()->can('review', $doc),
                        'approve' => auth()->user()->can('approve', $doc),
                        'viewRevisionHistory' => auth()->user()->can('viewRevisionHistory', $doc),
                        'setCode' => auth()->user()->can('setCode', $doc),
                    ],
                ];
            }),
        ]);
    }

    public function create()
    {
        $process_names = Section::where('company_id', \App\Support\CompanyContext::id())->orderBy('section_number')->get();
        return view('document.forms_manual.create', compact('process_names'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $incomingFields = $request->validate([
            'title' => 'required',
            'section_id' => 'required',
            'code' => 'required',
            'revision_number' => 'nullable',
            'pages' => 'required|integer',
            'justification' => 'required',
            'file' => 'required|mimes:pdf|max:20480',
        ]);

        $path = $request->file('file')->store('manuals', 'public');

        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            $newDocument = Form::create([
                'title' => $incomingFields['title'],
                'code' => $incomingFields['code'],
                'section_id' => $incomingFields['section_id'],
                'revision_number' => null,
                'effective_date' => null,
                'pages' => $incomingFields['pages'],
                'status' => 'Draft',
                'justification' => $incomingFields['justification'],
                'file_path' => $path,
            ]);

            ActivityLog::create([
                'action' => 'created draft',
                'description' => 'A forms document draft has been created.',
                'document_id' => $newDocument->id,
                'document_type' => 'form',
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()->back()->with("success","New Document Created Successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd(session()->all(), $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '.$e])->withInput();
        }
    }

    public function view(Form $doc)
    {
        return view('document.forms_manual.view', compact('doc'));
    }

    public function edit(Form $doc)
    {
        Gate::authorize('update', $doc);

        if (! $this->isEditable($doc)) {
            return redirect()->route('document.forms.index')->withErrors([
                'error' => 'This form cannot be edited while it is '. $doc->status .'.'
            ]);
        }

        $process_names = Section::where('company_id', \App\Support\CompanyContext::id())->orderBy('section_number')->get();
        return view('document.forms_manual.edit', compact('doc', 'process_names'));
    }

    /**
     * Editing a Draft / sent-back form updates it in place. Editing an Active form starts
     * a new Draft revision under the same code; the Active one stays in force until the new
     * revision is approved (approveOrNot supersedes it by code).
     */
    public function update(Request $request, Form $doc)
    {
        Gate::authorize('update', $doc);

        if (! $this->isEditable($doc)) {
            return back()->withErrors([
                'error' => 'This form cannot be edited while it is '. $doc->status .'.'
            ]);
        }

        $isNewRevision = $doc->status === 'Active';

        if ($isNewRevision && $this->hasRevisionInProgress($doc)) {
            return back()->withErrors([
                'error' => 'A revision of this form ('. $doc->code .') is already in progress.'
            ])->withInput();
        }

        $incomingFields = $request->validate([
            'title' => 'required',
            'section_id' => 'required',
            'code' => $isNewRevision ? 'nullable' : 'required',
            'pages' => 'required|integer',
            'justification' => 'required',
            'file' => 'nullable|mimes:pdf|max:20480',
        ]);

        DB::beginTransaction();

        try {
            $newPath = $request->hasFile('file')
                ? $request->file('file')->store('manuals', 'public')
                : null;

            if ($isNewRevision) {
                $target = Form::create([
                    'title' => $incomingFields['title'],
                    'code' => $doc->code,
                    'section_id' => $incomingFields['section_id'],
                    'revision_number' => null,
                    'effective_date' => null,
                    'pages' => $incomingFields['pages'],
                    'status' => 'Draft',
                    'justification' => $incomingFields['justification'],
                    'file_path' => $newPath ?? $doc->file_path,
                ]);

                ActivityLog::create([
                    'action' => 'created draft',
                    'description' => 'A new revision draft of form ('. $doc->code .') has been created.',
                    'document_id' => $target->id,
                    'document_type' => 'form',
                    'user_id' => auth()->id(),
                    'status_from' => null,
                    'status_to' => 'Draft'
                ]);
            } else {
                $oldStatus = $doc->status;
                $oldPath = $doc->file_path;

                $doc->update([
                    'title' => $incomingFields['title'],
                    'code' => $incomingFields['code'],
                    'section_id' => $incomingFields['section_id'],
                    'pages' => $incomingFields['pages'],
                    'status' => 'Draft',
                    'justification' => $incomingFields['justification'],
                    'file_path' => $newPath ?? $oldPath,
                ]);

                // Earlier revisions may share the same PDF, so only remove it once nothing points at it.
                if ($newPath && $oldPath && ! Form::withTrashed()->where('file_path', $oldPath)->exists()) {
                    Storage::disk('public')->delete($oldPath);
                }

                ActivityLog::create([
                    'action' => 'edited draft',
                    'description' => 'Form draft ('. $doc->code .') has been updated.',
                    'document_id' => $doc->id,
                    'document_type' => 'form',
                    'user_id' => auth()->id(),
                    'status_from' => $oldStatus,
                    'status_to' => 'Draft'
                ]);
            }

            DB::commit();

            return redirect()->route('document.forms.index')->with('success', $isNewRevision
                ? 'New revision draft created.'
                : 'Document updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (! empty($newPath)) {
                Storage::disk('public')->delete($newPath);
            }
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '. $e->getMessage()])->withInput();
        }
    }

    public function forReview(Form $doc)
    {
        if (!($doc->status == 'Draft' || $doc->status == 'For Revision')) {
            return back()->withErrors([
                'error' => 'Only draft and for revision documents can be sent for review.'
            ]);
        }

        Gate::authorize('sendForReview', $doc);

        DB::beginTransaction();

        try {
            $oldStatus = $doc->status;
            $doc->update(['status' => 'For Review']);

            ActivityLog::create([
                'action' => 'submitted for review',
                'description' => 'Form ('. $doc->code .') has been submitted for review.',
                'document_id' => $doc->id,
                'document_type' => 'form',
                'user_id' => auth()->id(),
                'status_from' => $oldStatus,
                'status_to' => 'For Review'
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Document sent for review.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. Failed to send document for review. '. $e->getMessage()]);
        }
    }

    public function reviewPassOrFail(Request $request, Form $doc)
    {
        if ($doc->status !== 'For Review') {
            return back()->withErrors([
                'error' => 'Invalid document status. Cannot proceed.'
            ]);
        }

        Gate::authorize('review', $doc);

        $validated = $request->validate([
            'decision' => 'required|in:pass,fail',
        ]);

        DB::beginTransaction();

        try {
            $newStatus = $validated['decision'] === 'pass'
                ? 'For Approval'
                : 'Review not Passed';

            $doc->update(['status' => $newStatus]);

            ActivityLog::create([
                'action' => $validated['decision'] === 'pass' ? 'review passed' : 'review failed',
                'description' => $validated['decision'] === 'pass'
                    ? 'Form ('. $doc->code .') passed review and was sent for approval.'
                    : 'Form ('. $doc->code .') failed review and was sent back.',
                'document_id' => $doc->id,
                'document_type' => 'form',
                'user_id' => auth()->id(),
                'status_from' => 'For Review',
                'status_to' => $newStatus
            ]);

            DB::commit();
            return redirect()->back()->with('success', $validated['decision'] === 'pass'
                ? 'Document passed review and was sent for approval.'
                : 'Document failed review.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. '. $e->getMessage()]);
        }
    }

    public function approveOrNot(Request $request, Form $doc)
    {
        if ($doc->status !== 'For Approval') {
            return back()->withErrors([
                'error' => 'Invalid document status. Cannot proceed.'
            ]);
        }

        Gate::authorize('approve', $doc);

        $validated = $request->validate([
            'decision' => 'required|in:pass,fail',
        ]);

        DB::beginTransaction();

        try {
            if ($validated['decision'] === 'pass') {
                // Get the latest ACTIVE form for this code (excluding current)
                $latestActive = Form::where('code', $doc->code)
                    ->where('status', 'Active')
                    ->where('id', '!=', $doc->id)
                    ->orderByDesc('created_at')
                    ->lockForUpdate()
                    ->first();

                // Newly created forms start at revision 0
                $newRevisionNumber = $latestActive
                    ? (int) $latestActive->revision_number + 1
                    : 0;

                if ($latestActive) {
                    $latestActive->update(['status' => 'Superseded']);

                    ActivityLog::create([
                        'action' => 'superseded',
                        'description' => 'Form ('. $latestActive->code .') revision '. $latestActive->revision_number .' has been superseded by revision '. $newRevisionNumber,
                        'document_id' => $latestActive->id,
                        'document_type' => 'form',
                        'user_id' => auth()->id(),
                        'status_from' => 'Active',
                        'status_to' => 'Superseded'
                    ]);
                }

                $newStatus = 'Pending Code';
                $doc->update([
                    'status' => $newStatus,
                    'revision_number' => $newRevisionNumber,
                    'effective_date' => now()->toDateString(),
                ]);
                $successMessage = 'Document has been approved.';
            } else {
                $newStatus = 'Not Approved';
                $doc->update(['status' => $newStatus]);
                $successMessage = 'Document not approved.';
            }

            ActivityLog::create([
                'action' => $validated['decision'] === 'pass' ? 'approved' : 'not approved',
                'description' => $validated['decision'] === 'pass'
                    ? 'Form ('. $doc->code .') has been approved.'
                    : 'Form ('. $doc->code .') has not been approved.',
                'document_id' => $doc->id,
                'document_type' => 'form',
                'user_id' => auth()->id(),
                'status_from' => 'For Approval',
                'status_to' => $newStatus
            ]);

            DB::commit();
            return redirect()->back()->with('success', $successMessage);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. '. $e->getMessage()]);
        }
    }

    public function assignCode(Request $request, Form $doc)
    {
        if ($doc->status !== 'Pending Code') {
            return back()->withErrors(['error' => 'This document is not ready for code assignment.']);
        }

        Gate::authorize('setCode', $doc);

        $validated = $request->validate([
            'document_code' => ['required', 'string', 'max:255'],
            'revision_number' => ['required', 'string', 'max:50']
        ]);

        DB::beginTransaction();

        try {
            $doc->update([
                'code' => $validated['document_code'],
                'revision_number' => $validated['revision_number'],
                'status' => 'Active',
            ]);

            ActivityLog::create([
                'action' => 'assigned code',
                'description' => 'Assigned code: ' . $validated['document_code'] . '; Revision: ' . $validated['revision_number'],
                'document_id' => $doc->id,
                'document_type' => 'form',
                'user_id' => auth()->id(),
                'status_from' => 'Pending Code',
                'status_to' => 'Active'
            ]);

            DB::commit();

            return back()->with('success', 'Document code and revision number assigned successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to assign document code.']);
        }
    }

    public function revisionHistory($code)
    {
        $revhistory = Form::where('code', $code)->orderBy('created_at')->get();
        abort_if($revhistory->isEmpty(), 404);

        Gate::authorize('viewRevisionHistory', $revhistory->first());

        return view('document.forms_manual.revision_history', compact('revhistory', 'code'));
    }

    public function destroy(Request $request, Form $doc)
    {
        Gate::authorize('delete', $doc);

        $request->validate([
            'delete_justification' => 'required|string|min:10'
        ]);

        DB::transaction(function () use ($doc, $request) {
            $oldStatus = $doc->status;

            $doc->update([
                'delete_justification' => $request->delete_justification,
                'status' => 'Archived'
            ]);

            ActivityLog::create([
                'action' => 'deleted document',
                'description' => "Archived Form: {$doc->code}. Justification: {$request->delete_justification}",
                'document_id' => $doc->id,
                'document_type' => 'form',
                'user_id' => auth()->id(),
                'status_from' => $oldStatus,
                'status_to' => 'Archived'
            ]);

            $doc->delete();
        });

        return redirect()->back()->with('success', 'Document has been successfully archived.');
    }

    private function isEditable(Form $doc): bool
    {
        return in_array($doc->status, ['Draft', 'For Revision', 'Review not Passed', 'Not Approved', 'Active'], true);
    }

    private function hasRevisionInProgress(Form $doc): bool
    {
        return Form::where('code', $doc->code)
            ->where('id', '!=', $doc->id)
            ->whereNotIn('status', ['Active', 'Superseded'])
            ->exists();
    }
}
