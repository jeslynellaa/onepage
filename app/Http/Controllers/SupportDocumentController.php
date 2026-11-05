<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;

class SupportDocumentController extends Controller
{

    public function index() {
        $documents = SupportDocument::all();
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
        return view('document.support_documents.index', compact('documents', 'sections', 'totalCount', 'user_list'));
    }


    public function getSpSectionDocuments(Request $request)
    {
        $sectionId = $request->input('sectionId');

        $items = SupportDocument::where('section_id', $sectionId)
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
                    'viewUrl' => route('document.support_document.view', $doc->id),
                    'editUrl' => route('document.support_document.edit', $doc->id),
                    'deleteUrl' => route('document.support_document.destroy', $doc->id),
                    'revHistoryUrl' => route('document.support_document.rev_history', $doc->code),
                    'sendForReviewUrl' => route('document.support_document.forReview', $doc->id),
                    'reviewDecisionUrl' => route('document.support_document.reviewPassOrFail', $doc->id),
                    'approveDecisionUrl' => route('document.support_document.approveOrNot', $doc->id),
                    'assignCodeUrl' => route('document.support_document.assignCode', $doc->id),

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
        return view('document.support_documents.create', compact('process_names'));
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
            'objective' => 'required',
            'justification' => 'required',
            'scope' => 'required',
            'file' => 'required|mimes:pdf|max:20480',
        ]);

        $path = $request->file('file')->store('manuals', 'public');

        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            $incomingFields['objective'] = Str::markdown($incomingFields['objective']);
            $incomingFields['scope'] = Str::markdown($incomingFields['scope']);

            $newDocument = SupportDocument::create([
                'title' => $incomingFields['title'],
                'code' => $incomingFields['code'],
                'section_id' => $incomingFields['section_id'],
                'revision_number' => null,
                'effective_date' => null,
                'objective' => $incomingFields['objective'],
                'scope' => $incomingFields['scope'],
                'pages' => $incomingFields['pages'],
                'status' => 'Draft',
                'justification' => $incomingFields['justification'],
                'file_path' => $path,
            ]);

            ActivityLog::create([
                'action' => 'created draft',
                'description' => 'Support Document draft has been created.',
                'document_id' => $newDocument->id,
                'document_type' => 'support_document',
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

    public function view(SupportDocument $doc)
    {
        $canEdit = $this->isEditable($doc) && auth()->user()->can('update', $doc);

        return view('document.support_documents.view', compact('doc', 'canEdit'));
    }

    public function edit(SupportDocument $doc)
    {
        Gate::authorize('update', $doc);

        if (! $this->isEditable($doc)) {
            return redirect()->route('document.support_document.index')->withErrors([
                'error' => 'This document cannot be edited while it is '. $doc->status .'.'
            ]);
        }

        $converter = new HtmlConverter();
        $doc->scope = $converter->convert($doc->scope);
        $doc->objective = $converter->convert($doc->objective);

        $process_names = Section::where('company_id', \App\Support\CompanyContext::id())->orderBy('section_number')->get();
        return view('document.support_documents.edit', compact('doc', 'process_names'));
    }

    /**
     * Editing a Draft / sent-back document updates it in place. Editing an Active document starts
     * a new Draft revision under the same code; the Active one stays in force until the new
     * revision is approved (approveOrNot supersedes it by code).
     */
    public function update(SupportDocument $doc, Request $request)
    {
        Gate::authorize('update', $doc);

        if (! $this->isEditable($doc)) {
            return back()->withErrors([
                'error' => 'This document cannot be edited while it is '. $doc->status .'.'
            ]);
        }

        $isNewRevision = $doc->status === 'Active';

        if ($isNewRevision && $this->hasRevisionInProgress($doc)) {
            return back()->withErrors([
                'error' => 'A revision of this document ('. $doc->code .') is already in progress.'
            ])->withInput();
        }

        $incomingFields = $request->validate([
            'title' => 'required',
            'section_id' => 'required',
            'code' => $isNewRevision ? 'nullable' : 'required',
            'pages' => 'required|integer',
            'objective' => 'required',
            'justification' => 'required',
            'scope' => 'required',
            'file' => 'nullable|mimes:pdf|max:20480',
        ]);

        DB::beginTransaction();

        try {
            $incomingFields['objective'] = Str::markdown($incomingFields['objective']);
            $incomingFields['scope'] = Str::markdown($incomingFields['scope']);

            $newPath = $request->hasFile('file')
                ? $request->file('file')->store('manuals', 'public')
                : null;

            if ($isNewRevision) {
                $target = SupportDocument::create([
                    'title' => $incomingFields['title'],
                    'code' => $doc->code,
                    'section_id' => $incomingFields['section_id'],
                    'revision_number' => null,
                    'effective_date' => null,
                    'objective' => $incomingFields['objective'],
                    'scope' => $incomingFields['scope'],
                    'pages' => $incomingFields['pages'],
                    'status' => 'Draft',
                    'justification' => $incomingFields['justification'],
                    'file_path' => $newPath ?? $doc->file_path,
                ]);

                ActivityLog::create([
                    'action' => 'created draft',
                    'description' => 'A new revision draft of support document ('. $doc->code .') has been created.',
                    'document_id' => $target->id,
                    'document_type' => 'support_document',
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
                    'objective' => $incomingFields['objective'],
                    'scope' => $incomingFields['scope'],
                    'pages' => $incomingFields['pages'],
                    'status' => 'Draft',
                    'justification' => $incomingFields['justification'],
                    'file_path' => $newPath ?? $oldPath,
                ]);

                // Earlier revisions may share the same PDF, so only remove it once nothing points at it.
                if ($newPath && $oldPath && ! SupportDocument::withTrashed()->where('file_path', $oldPath)->exists()) {
                    Storage::disk('public')->delete($oldPath);
                }

                ActivityLog::create([
                    'action' => 'edited draft',
                    'description' => 'Support Document draft ('. $doc->code .') has been updated.',
                    'document_id' => $doc->id,
                    'document_type' => 'support_document',
                    'user_id' => auth()->id(),
                    'status_from' => $oldStatus,
                    'status_to' => 'Draft'
                ]);
            }

            DB::commit();

            return redirect()->route('document.support_document.index')->with('success', $isNewRevision
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

    public function forReview(SupportDocument $doc)
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
                'description' => 'Support Document ('. $doc->code .') has been submitted for review.',
                'document_id' => $doc->id,
                'document_type' => 'support_document',
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

    public function reviewPassOrFail(Request $request, SupportDocument $doc)
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
                    ? 'Support Document ('. $doc->code .') passed review and was sent for approval.'
                    : 'Support Document ('. $doc->code .') failed review and was sent back.',
                'document_id' => $doc->id,
                'document_type' => 'support_document',
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

    public function approveOrNot(Request $request, SupportDocument $doc)
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
                // Get the latest ACTIVE revision for this code (excluding current)
                $latestActive = SupportDocument::where('code', $doc->code)
                    ->where('status', 'Active')
                    ->where('id', '!=', $doc->id)
                    ->orderByDesc('created_at')
                    ->lockForUpdate()
                    ->first();

                // Newly created documents start at revision 0
                $newRevisionNumber = $latestActive
                    ? (int) $latestActive->revision_number + 1
                    : 0;

                if ($latestActive) {
                    $latestActive->update(['status' => 'Superseded']);

                    ActivityLog::create([
                        'action' => 'superseded',
                        'description' => 'Support Document ('. $latestActive->code .') revision '. $latestActive->revision_number .' has been superseded by revision '. $newRevisionNumber,
                        'document_id' => $latestActive->id,
                        'document_type' => 'support_document',
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
                    ? 'Support Document ('. $doc->code .') has been approved.'
                    : 'Support Document ('. $doc->code .') has not been approved.',
                'document_id' => $doc->id,
                'document_type' => 'support_document',
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

    public function assignCode(Request $request, SupportDocument $doc)
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
                'document_type' => 'support_document',
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
        $revhistory = SupportDocument::where('code', $code)->orderBy('created_at')->get();
        abort_if($revhistory->isEmpty(), 404);

        Gate::authorize('viewRevisionHistory', $revhistory->first());

        return view('document.support_documents.revision_history', compact('revhistory', 'code'));
    }

    public function destroy(Request $request, SupportDocument $doc)
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
                'description' => "Archived Support Document: {$doc->code}. Justification: {$request->delete_justification}",
                'document_id' => $doc->id,
                'document_type' => 'support_document',
                'user_id' => auth()->id(),
                'status_from' => $oldStatus,
                'status_to' => 'Archived'
            ]);

            $doc->delete();
        });

        // Not back(): that's the archived document's view page when archived from there.
        return redirect()->route('document.support_document.index')->with('success', 'Document has been successfully archived.');
    }

    private function isEditable(SupportDocument $doc): bool
    {
        return in_array($doc->status, ['Draft', 'For Revision', 'Review not Passed', 'Not Approved', 'Active'], true);
    }

    private function hasRevisionInProgress(SupportDocument $doc): bool
    {
        return SupportDocument::where('code', $doc->code)
            ->where('id', '!=', $doc->id)
            ->whereNotIn('status', ['Active', 'Superseded'])
            ->exists();
    }
}
