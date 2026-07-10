<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MsManual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsManualController extends Controller
{
    public function index()
    {
        $ms_manuals = MsManual::all();
        
        $docs = $ms_manuals->map(function ($doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'section_number' => $doc->section_number,
                'revision_number' => $doc->revision_number,
                'pages' => $doc->pages,
                'effective_date' => $doc->effective_date,
                'status' => $doc->status,

                // URLs
                'viewUrl' => route('document.ms_manual.view', $doc->id),
                'editUrl' => route('document.ms_manual.edit', $doc->id),
                'deleteUrl' => route('document.ms_manual.destroy', $doc->id),
                'revHistoryUrl' => route('document.ms_manual.rev_history', $doc->id),
                'sendForReviewUrl' => route('document.ms_manual.forReview', $doc->id),
                'reviewDecisionUrl' => route('document.ms_manual.reviewPassOrFail', $doc->id),
                'approveDecisionUrl' => route('document.ms_manual.approveOrNot', $doc->id),

                // 🔐 AUTH FLAGS (Policy-based)
                'can' => [
                    'edit' => auth()->user()->can('update', $doc),
                    'delete' => auth()->user()->can('delete', $doc),
                    'send' => auth()->user()->can('sendForReview', $doc),
                    'review' => auth()->user()->can('review', $doc),
                    'approve' => auth()->user()->can('approve', $doc),
                    'viewRevisionHistory' => auth()->user()->can('viewRevisionHistory', $doc)
                ],
            ];
        });
        // dd($docs);
        return view('document.ms_manual.index', compact('docs'));
    }

    public function create()
    {
        return view('document.ms_manual.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'section_number' => 'required',
            'pages' => 'required|integer',
            'justification' => 'required',
            'file' => 'required|mimes:pdf|max:20480',
        ]);

        $path = $request->file('file')->store('manuals', 'public');

        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            $newDocument = MsManual::create([
                'title' => $request->title,
                'section_number' => $request->section_number,
                'revision_number' => null,
                'pages' => $request->pages,
                'effective_date' => null,
                'status' => 'Draft',
                'justification' => $request->justification,
                'file_path' => $path,
            ]);

            ActivityLog::create([
                'action' => 'created draft',
                'description' => 'MS Manual document draft has been created.',
                'document_id' => $newDocument->id,
                'document_type' => 'ms_manual',
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

    public function edit(MsManual $doc)
    {
        return view('document.ms_manual.edit', compact('doc'));
    }

    public function update(Request $request, MsManual $doc) {
        $request->validate([
            'title' => 'required',
            'section_number' => 'required',
            'pages' => 'required|integer',
            'justification' => 'required',
            'file' => 'sometimes|file|mimes:pdf|max:20480', // optional on update
        ]);

        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            $updateData = [
                'title' => $request->title,
                'section_number' => $request->section_number,
                'revision_number' => null,
                'pages' => $request->pages,
                'effective_date' => null,
                'status' => 'Draft',
                'justification' => $request->justification,
            ];

            // Conditionally add the file path if a new file exists
            if ($request->hasFile('file')) {
                // Delete old file here if desired
                $updateData['file_path'] = $request->file('file')->store('manuals', 'public');
            }

            // Save everything to the database in exactly ONE query
            $doc->update($updateData);

            ActivityLog::create([
                'action' => 'edited draft',
                'description' => 'MS Manual document draft has been updated.',
                'document_id' => $doc->id,
                'document_type' => 'ms_manual',
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()->back()->with("success","Document Updated Successfully");
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd(session()->all(), $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '.$e])->withInput();
        }
    }

    public function view(MsManual $doc)
    {
        return view('document.ms_manual.view', compact('doc'));
    }

    public function destroy(Request $request, MsManual $doc)
    {
        $request->validate([
            'delete_justification' => 'required|string|min:10'
        ]);

        \DB::transaction(function () use ($doc, $request) {
            $oldStatus = $doc->status;

            $doc->update([
                'delete_justification' => $request->delete_justification,
                'status' => 'Archived' 
            ]);

            // 3. Create the Activity Log
            ActivityLog::create([
                'action' => 'deleted document',
                'description' => "Archived MS Manual Document: {$doc->title}. Justification: {$request->delete_justification}",
                'document_id' => $doc->id,
                'document_type' => 'ms_manual',
                'user_id' => auth()->id(),
                'status_from' => $oldStatus,
                'status_to' => 'Archived'
            ]);

            // 4. Perform the Soft Delete
            $doc->delete();
        });

        return redirect()->back()->with('success', 'Document has been successfully archived.');
    }
}
