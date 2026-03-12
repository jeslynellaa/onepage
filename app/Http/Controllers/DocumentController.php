<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dirf;
use App\Models\Document;
use App\Models\ProcedureComments;
use App\Models\ProcedureSteps;
use App\Models\Section;
use App\Models\StepDocuments;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    public function index() {
        $lastActivity = optional(
            ActivityLog::latest('performed_at')->value('performed_at')
        )->format('d M Y');

            
        return view('document.index', compact('lastActivity'));
    }

    public function system_procedures() {
        $documents = Document::all();
        $sections = Section::with(['processOwner', 'reviewer', 'approver'])->get();
        $user_list = User::all();
        $users = User::orderBy('last_name', 'ASC')
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
        return view('document.system_procedures.index', compact('documents', 'sections', 'totalCount', 'user_list'));
    }

    public function sp_create() {
        return view('document.system_procedures.create');
    }

    public function sp_store(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'code' => 'required',
            'section_id' => 'required',
            'revision_number' => 'nullable',
            'effective_date' => 'nullable|date',
            'objective' => 'required',
            'justification' => 'required',
            'scope' => 'required',
            'type' => 'required',
        ]);
        
        $procedureSteps = json_decode($request->input('procedure_steps_json'), true);

        // Ensure it's an array with at least one entry
        if (!is_array($procedureSteps) || count($procedureSteps) === 0) {
            return back()->withErrors([
                'procedure_steps_json' => 'At least one prcodeure step is required.'
            ])->withInput();
        }
        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            unset($incomingFields['procedure_steps_json']);
            $incomingFields['objective'] = Str::markdown($incomingFields['objective']);
            $incomingFields['scope'] = Str::markdown($incomingFields['scope']);
            $newDocument = Document::create(array_merge($incomingFields, ['status' => 'Draft']));

            foreach ($procedureSteps as $step) {
                // dd($step, $step['interfaces_input'], $step['interfaces_output']);
                $procedureStep = ProcedureSteps::create([
                    'document_id' => $newDocument->id,
                    'responsibility' => $step['responsibility'],
                    'activities' => $step['activities'],
                    'note' => !(empty($step['note']) || $step['note'] == '') ? Str::markdown($step['note']) : null,
                ]);

                // --- Save inputs (references)
                if (!empty($step['interfaces_input'])) {
                    foreach ($step['interfaces_input'] as $input) {
                        StepDocuments::create([
                            'type' => 'input',
                            'category' => $input['category'] ?? null,
                            'title' => $input['name'] ?? null,
                            'procedure_step_id' => $procedureStep->id
                        ]);
                    }
                }

                // --- Save outputs
                if (!empty($step['interfaces_output'])) {
                    foreach ($step['interfaces_output'] as $output) {
                        StepDocuments::create([
                            'type' => 'output',
                            'category' => $output['category'] ?? null,
                            'title' => $output['name'] ?? null,
                            'procedure_step_id' => $procedureStep->id
                        ]);
                    }
                }
            }
            ActivityLog::create([
                'action' => 'created draft',
                'description' => 'System Procedure document draft has been created.',
                'document_id' => $newDocument->id,
                'document_type' => 'system_procedure',
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

    public function sp_view(Document $doc)
    {
        $reviewComments = $doc->comments()
            ->with('user')
            ->where('stage', 'review')
            ->latest()
            ->get()
            ->groupBy('section');
        $approvalComments = $doc->comments()
            ->with('user')
            ->where('stage', 'approval')
            ->latest()
            ->get()
            ->groupBy('section');

        $sectionOrder = [
            'title', 'objectives', 'scope', 'flowchart', 'notes', 'interfaces', 'remarks'
        ];

        $sectionLabels = [
            'title' => 'Title',
            'objectives' => 'Objective/s',
            'scope' => 'Scope',
            'flowchart' => 'Process Flowchart',
            'notes' => 'Notes',
            'interfaces' => 'Interfaces',
            'remarks' => 'Other Remarks',
        ];

        return view('document.system_procedures.view', compact('doc', 'reviewComments', 'approvalComments', 'sectionOrder', 'sectionLabels'));
    }

    private function getTextColorForBackground($hex) {
        $hex = ltrim($hex, '#');

        // Support shorthand hex (#fff)
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return $brightness < 128 ? '#ffffff' : '#000000';
    }


    public function sp_edit(Document $doc) {
        $converter = new HtmlConverter();
        $doc->scope = $converter->convert($doc->scope);
        $doc->objective = $converter->convert($doc->objective);

        $doc->load('steps.interfaces');

        // Transform expense details to include interfaces title
        $steps = $doc->steps->map(function ($detail) {
            return array_merge(
                $detail->toArray(),
                [
                    'interfaces_input' => $detail->interfaces
                        ->where('type', 'input')
                        ->map(fn($i) => ['name' => $i->title, 'category' => $i->category])
                        ->values()
                        ->toArray(),

                    'interfaces_output' => $detail->interfaces
                        ->where('type', 'output')
                        ->map(fn($i) => ['name' => $i->title, 'category' => $i->category])
                        ->values()
                        ->toArray(),
                ]
            );
        });

        // dd($steps);
        return view('document.system_procedures.edit', [
            'doc' => $doc,
            'procedureStepsJson' => $steps->toJson()
        ]);
    }

    public function sp_update(Request $request, Document $doc) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'code' => 'required',
            'section_id' => 'required',
            'revision_number' => 'nullable',
            'effective_date' => 'nullable|date',
            'objective' => 'required',
            'justification' => 'nullable',
            'scope' => 'required',
            'type' => 'required',
        ]);
        
        $procedureSteps = json_decode($request->input('procedure_steps_json'), true);
        
        // Ensure it's an array with at least one entry
        if (!is_array($procedureSteps) || count($procedureSteps) === 0) {
            return back()->withErrors([
                'procedure_steps_json' => 'At least one procedure step is required.'
            ])->withInput();
        }
        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            // Update main document
            $incomingFields['objective'] = Str::markdown($incomingFields['objective']);
            $incomingFields['scope'] = Str::markdown($incomingFields['scope']);

            $doc->update($incomingFields);

            // Remove existing steps + step documents
            foreach ($doc->steps as $step) {
                $step->interfaces()->delete();
            }
            $doc->steps()->delete();

            // Re-create steps
            foreach ($procedureSteps as $step) {
                $procedureStep = ProcedureSteps::create([
                    'document_id' => $doc->id,
                    'responsibility' => $step['responsibility'],
                    'activities' => $step['activities'],
                    'note' => !(empty($step['note']) || $step['note'] == '') ? Str::markdown($step['note']) : null,
                ]);

                // --- Save inputs (references)
                if (!empty($step['interfaces_input'])) {
                    foreach ($step['interfaces_input'] as $input) {
                        StepDocuments::create([
                            'type' => 'input',
                            'category' => $input['category'] ?? null,
                            'title' => $input['name'] ?? null,
                            'procedure_step_id' => $procedureStep->id
                        ]);
                    }
                }

                // --- Save outputs
                if (!empty($step['interfaces_output'])) {
                    foreach ($step['interfaces_output'] as $output) {
                        StepDocuments::create([
                            'type' => 'output',
                            'category' => $output['category'] ?? null,
                            'title' => $output['name'] ?? null,
                            'procedure_step_id' => $procedureStep->id
                        ]);
                    }
                }
            }

            ActivityLog::create([
                'action' => 'edited draft',
                'description' => 'System Procedure document draft ('. $doc->code .') has been updated.',
                'status_from' => 'created draft',
                'status_to' => 'edited draft',
                'document_id' => $doc->id,
                'document_type' => 'system_procedure',
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Document updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd(session()->all());
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '. $e->getMessage()])->withInput();
        }
    }

    public function sp_forReview(Document $doc) {
        if (!($doc->status == 'Draft' || $doc->status == 'For Revision')) {
            return back()->withErrors([
                'error' => 'Only draft and for revision documents can be sent for review.'
            ]);
        }
        
        DB::beginTransaction();

        try {
            $doc->update(['status' => 'For Review']);

            ActivityLog::create([
                'action' => 'submitted for review',
                'description' => 'System Procedure document ('. $doc->code .') has been submitted for review.',
                'document_id' => $doc->id,
                'document_type' => 'system_procedure',
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Document sent for review.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. Failed to send document for review. '. $e->getMessage()]);
        }
    }

    public function sp_reviewPassOrFail(Request $request, Document $doc) {
        if ($doc->status !== 'For Review') {
            return back()->withErrors([
                'error' => 'Invalid document status. Cannot proceed.'
            ]);
        }

        // Validate reviewer decision
        $validated = $request->validate([
            'decision' => 'required|in:pass,fail',
        ]);
        
        DB::beginTransaction();

        try {
            $newStatus = $validated['decision'] === 'pass'
                ? 'For Approval'
                : 'Review not Passed';

            $doc->update(['status' => $newStatus,]);

            // create activity log
            ActivityLog::create([
                'action' => $validated['decision'] === 'pass' ? 'review passed' : 'review failed',
                'description' => $validated['decision'] === 'pass'
                    ? 'System Procedure document ('. $doc->code .') passed review and was sent for approval.'
                    : 'System Procedure document ('. $doc->code .') failed review and was sent back.',
                'document_id' => $doc->id,
                'document_type' => 'system_procedure',
                'user_id' => auth()->id()
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

    public function sp_approveOrNot(Request $request, Document $doc) {
        if ($doc->status !== 'For Approval') {
            return back()->withErrors([
                'error' => 'Invalid document status. Cannot proceed.'
            ]);
        }

        // Validate reviewer decision
        $validated = $request->validate([
            'decision' => 'required|in:pass,fail',
        ]);
        
        DB::beginTransaction();

        try {
            if ($validated['decision'] === 'pass') {
                // 🔎 Get the latest ACTIVE document for this code (excluding current)
                $latestActive = Document::where('code', $doc->code)
                    ->where('status', 'Active')
                    ->where('id', '!=', $doc->id)
                    ->orderByDesc('revision_number')
                    ->lockForUpdate() // prevents race conditions
                    ->first();

                $newRevisionNumber = $latestActive
                    ? $latestActive->revision_number + 1
                    : 0;                // For newly created procedures, revision no will start at 0

                // Supersede the old active document
                if ($latestActive) {
                    $latestActive->update([
                        'status' => 'Superseded',
                    ]);

                    // Log the superseded document
                    ActivityLog::create([
                        'action' => 'superseded',
                        'description' => 'System Procedure document ('. $latestActive->code .') revision '. $latestActive->revision_number .' has been superseded by revision '. $newRevisionNumber,
                        'document_id' => $latestActive->id,
                        'document_type' => 'system_procedure',
                        'user_id' => auth()->id(),
                        'status_from' => 'Active',
                        'status_to' => 'Superseded'
                    ]);
                }

                // ✅ Activate the new document
                $doc->update([
                    'status' => 'Pending Code',
                    'revision_number' => $newRevisionNumber,
                    'effective_date' => now()->toDateString(), // YYYY-MM-DD
                ]);
                $successMessage = 'Document has been approved.';
            }else{
                // Review failed
                $doc->update(['status' => 'Not Approved',]);

                $successMessage = 'Document not approved.';
            }

            // create activity log
            ActivityLog::create([
                'action' => $validated['decision'] === 'pass' ? 'approved' : 'not approved',
                'description' => $validated['decision'] === 'pass'
                    ? 'System Procedure document ('. $doc->code .') has been approved.'
                    : 'System Procedure document ('. $doc->code .') has not been approved.',
                'document_id' => $doc->id,
                'document_type' => 'system_procedure',
                'user_id' => auth()->id()
            ]);
            DB::commit();
            return redirect()->back()->with('success', $successMessage);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. '. $e->getMessage()]);
        }
    }

    public function assignCode(Request $request, Document $doc)
    {
        if ($doc->status !== 'Pending Code') {
            return back()->withErrors(['error' => 'This document is not ready for code assignment.']);
        }

        $validated = $request->validate([
            'document_code' => ['required', 'string', 'max:255'],
            'revision_number' => ['required', 'string', 'max:50']
        ]);

        DB::beginTransaction();

        try {
            $doc->code = $validated['document_code'];
            $doc->revision_number = $validated['revision_number'];
            $doc->status = 'Active';
            $doc->save();

            ActivityLog::create([
                'action' => 'assigned code',
                'description' => 'Assigned code: ' . $validated['document_code'] . '; Revision: ' . $validated['revision_number'],
                'document_id' => $doc->id,
                'document_type' => 'system_procedure',
                'user_id' => auth()->id(),
                'status_from' => 'Pending Code',
                'status_to' => 'Active'
            ]);

            DB::commit();

            return back()->with('success', 'Document code and revision number assigned successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to assign document code.');
        }
    }

    public function destroy(Document $doc)
    {
        try {
            DB::beginTransaction();

            $doc->delete();

            DB::commit();
            return redirect()->route('document.system_procedures')->with('success', 'Document deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete document. ' . $e->getMessage()]);
        }
    }

    public function getSectionDocuments(Request $request)
    {
        $sectionId = $request->input('sectionId');

        $items = Document::where('section_id', $sectionId)
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
                    'viewUrl' => route('document.system_procedures.view_pdf', $doc->id),
                    'editUrl' => route('document.system_procedures.edit', $doc->id),
                    'deleteUrl' => route('document.system_procedures.destroy', $doc->id),
                    'revHistoryUrl' => route('document.system_procedures.rev_history', $doc->code),
                    'sendForReviewUrl' => route('document.system_procedures.forReview', $doc->id),
                    'reviewDecisionUrl' => route('document.system_procedures.reviewPassOrFail', $doc->id),
                    'approveDecisionUrl' => route('document.system_procedures.approveOrNot', $doc->id),
                    'dirfUrl' => route('document.system_procedures.dirf_edit', $doc->id),

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
    
    public function sp_document_history($code)
    {
        $revhistory = Document::where('code', $code)->get();
        return view('document.system_procedures.revision_history', compact('revhistory'));
    }

    public function dirf_generate($doc){
        // 1️⃣ Load your Blade view into Dompdf
        $pdf = Pdf::loadView('pdf.dirf');
        
        // 2️⃣ Force rendering so Dompdf can calculate pages
        $pdf->output();

        // 3️⃣ Access the underlying Dompdf instance
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();

        // 4️⃣ Get total page count and store in the database
        // $totalPages = $canvas->get_page_count();
        // $doc->update([
        //     'pages' => $totalPages
        // ]);

        // 4️⃣ Add automatic page numbering
        // $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
        //     $text = "Page $pageNumber of $pageCount";
        //     $font = $fontMetrics->get_font("Helvetica", "normal");
        //     $size = 11;

        //     $canvas->text(455, 138, "Page $pageNumber of $pageCount", $font, $size);
        // });

        // 5️⃣ Stream or download
        return $pdf->stream();
    }

    public function dirf_edit(Document $doc) {
        $dirf = Dirf::where('document_id', '=', $doc->id);
        
        return view('document.system_procedures.dirf.edit', [
            'dirf' => $dirf
        ]);
    }

    public function showCommentForm(Document $doc)
    {
        $existingComments = $doc->comments()
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('section');
        $pageTitle = $doc->status === 'For Approval' ? 'Approver Comments' : 'Reviewer Comments';

        return view('document.system_procedures.comments', compact('doc', 'existingComments', 'pageTitle'));
    }

    public function preview(Document $doc)
    {
        // Load steps and their related documents
        $doc->load([
            'steps.interfaces',
            'section.processOwner',
            'section.reviewer',
            'section.approver',
        ]);
        $steps = $doc->steps;

        $submitted = $doc->logs->firstWhere('action', 'submitted for review');
        $passed = $doc->logs->firstWhere('action', 'review passed');
        $approved = $doc->logs->firstWhere('action', 'approved');
        $color = $doc->company->hex_code;
        $text_color = $this->getTextColorForBackground($color);

        if (app()->environment('production')) {
            $logo = $doc->company->logo_path
                ? Storage::disk('public')->path($doc->company->logo_path)
                : null;
            $owner_sign = $doc->section->processOwner->signature_path
                ? Storage::disk('public')->path($doc->section->processOwner->signature_path)
                : null;
            $reviewer_sign = $doc->section->reviewer->signature_path
                ? Storage::disk('public')->path($doc->section->reviewer->signature_path)
                : null;
            $approver_sign = $doc->section->approver->signature_path
                ? Storage::disk('public')->path($doc->section->approver->signature_path)
                : null;
            $connector = public_path('img/flowchart-connector.png');
        } else {
            $logo = public_path('storage/' . $doc->company->logo_path);
            $owner_sign = public_path('storage/' . $doc->section->processOwner->signature_path);
            $reviewer_sign = public_path('storage/' . $doc->section->reviewer->signature_path);
            $approver_sign = public_path('storage/' . $doc->section->approver->signature_path);
            $connector = realpath(base_path()).'\public\img\flowchart-connector.png';
        }

        // Flatten all interfaces into one collection
        $allInterfaces = $doc->steps->flatMap(function ($step) {
            return $step->interfaces;
        });

        // Separate and deduplicate by type and title
        $uniqueInputs = $allInterfaces
            ->where('type', 'input')
            ->unique('title')
            ->values();

        $uniqueOutputs = $allInterfaces
            ->where('type', 'output')
            ->unique('title')
            ->values();

        // 1️⃣ Load your Blade view into Dompdf
        $pdf = Pdf::loadView('pdf.system_procedure', compact('doc', 'steps', 'uniqueInputs', 'uniqueOutputs', 'connector', 'submitted', 'passed', 'approved', 'owner_sign', 'reviewer_sign', 'approver_sign', 'logo', 'color', 'text_color'))
                ->setPaper('A4', 'portrait');

        // 2️⃣ Force rendering so Dompdf can calculate pages
        $pdf->output();

        // 3️⃣ Access the underlying Dompdf instance
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();

        // 4️⃣ Get total page count and store in the database
        $totalPages = $canvas->get_page_count();
        $doc->update([
            'pages' => $totalPages
        ]);

        // 4️⃣ Add automatic page numbering
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Page $pageNumber of $pageCount";
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 11;

            // adjust these coordinates to fit your footer area (x, y)
            $canvas->text(455, 111, "Page $pageNumber of $pageCount", $font, $size);
        });

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice.pdf"');
    }

    public function storeComment(Request $request, Document $doc)
    {
        $request->validate([
            'title_comment' => ['nullable', 'string'],
            'objectives_comment' => ['nullable', 'string'],
            'scope_comment' => ['nullable', 'string'],
            'flowchart_comment' => ['nullable', 'string'],
            'notes_comment' => ['nullable', 'string'],
            'interfaces_comment' => ['nullable', 'string'],
            'remarks_comment' => ['nullable', 'string'],
            'action' => ['required', 'in:save,send_back'],
        ]);

        $comments = [
            'title' => $request->title_comment,
            'objectives' => $request->objectives_comment,
            'scope' => $request->scope_comment,
            'flowchart' => $request->flowchart_comment,
            'notes' => $request->notes_comment,
            'interfaces' => $request->interfaces_comment,
            'remarks' => $request->remarks_comment,
        ];

        $hasComment = collect($comments)
            ->filter(fn ($c) => !is_null($c) && trim($c) !== '')
            ->isNotEmpty();

        if ($request->action === 'send_back' && !$hasComment) {
            return redirect()->back()->withInput()->withErrors([
                'action' => 'You must add at least one comment before sending the document back.'
            ]);
        }

        DB::beginTransaction();

        try {
            // remove previous draft comments of this user for this doc
            ProcedureComments::where('document_id', $doc->id)
                ->where('user_id', auth()->id())
                ->delete();

            foreach ($comments as $section => $comment) {
                if (!is_null($comment) && trim($comment) !== '') {
                    ProcedureComments::create([
                        'document_id' => $doc->id,
                        'user_id' => auth()->id(),
                        'section' => $section,
                        'comment' => trim($comment),
                    ]);

                }
            }

            // update document if sending back
            if ($request->action === 'send_back') {
                $doc->status = 'For Revision';
                $doc->save();
            }
            if ($doc->status === 'For Review'){
                $commenter = 'Reviewer';
            }else{
                $commenter = 'Approver';
            }

            // create activity log
            ActivityLog::create([
                'action' => $request->action === 'send_back'
                    ? 'Sent back with Comments'
                    : 'Gave Comments',
                'description' => $request->action === 'send_back'
                    ? 'System Procedure document ('. $doc->code .') has been sent back by '. $commenter .' with comments to be revised.'
                    : $commenter . ' has made some comments to System Procedure document ('. $doc->code .').',
                'document_id' => $doc->id,
                'document_type' => 'system_procedure',
                'user_id' => auth()->id()
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '.$e])->withInput();
        }

        if ($request->action === 'send_back') {
            return redirect()->back()->with('success','Comments saved and document sent back.');
        }

        return redirect()->back()->with('success','Comments saved.');
    }
}
