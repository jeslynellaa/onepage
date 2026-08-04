<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;

class SupportDocumentController extends Controller
{

    public function index() {
        $documents = SupportDocument::all();
        $sections = Section::where('company_id', \App\Support\CompanyContext::id())->with(['processOwner', 'reviewer', 'approver'])->get();
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

                    // 🔐 AUTH FLAGS (Policy-based)
                    'can' => [
                        'edit' => auth()->user()->can('update', $doc),
                        'delete' => auth()->user()->can('delete', $doc),
                        'send' => auth()->user()->can('sendForReview', $doc),
                        'review' => auth()->user()->can('review', $doc),
                        'approve' => auth()->user()->can('approve', $doc),
                        'viewRevisionHistory' => auth()->user()->can('viewRevisionHistory', $doc),
                    ],
                ];
            }),
        ]);
    }

    public function create()
    {
        $process_names = Section::where('company_id', \App\Support\CompanyContext::id())->get();
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
        return view('document.support_documents.view', compact('doc'));
    }

    public function edit(SupportDocument $doc)
    {
        $converter = new HtmlConverter();
        $doc->scope = $converter->convert($doc->scope);
        $doc->objective = $converter->convert($doc->objective);

        return view('document.support_documents.edit', compact('doc'));
    }

    public function update(SupportDocument $doc, Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'section_id' => 'required',
            'code' => 'required',
            'revision_number' => 'nullable',
            'pages' => 'required|integer',
            'objective' => 'required',
            'justification' => 'required',
            'scope' => 'required',
            'file' => 'nullable|mimes:pdf|max:20480', // Changed to nullable
        ]);

        // Transaction - all or nothing
        DB::beginTransaction();

        try {
            // 2. Parse Markdown fields
            $incomingFields['objective'] = Str::markdown($incomingFields['objective']);
            $incomingFields['scope'] = Str::markdown($incomingFields['scope']);

            // 3. Handle file upload ONLY if a new file is provided
            if ($request->hasFile('file')) {
                // Delete old physical file if it exists
                if ($doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                // Store new file
                $path = $request->file('file')->store('manuals', 'public');
                $doc->file_path = $path;
            }

            // 4. Update the rest of the document attributes
            $doc->update([
                'title' => $incomingFields['title'],
                'code' => $incomingFields['code'],
                'section_id' => $incomingFields['section_id'],
                'revision_number' => null,
                'effective_date' => null,
                'objective' => $incomingFields['objective'],
                'scope' => $incomingFields['scope'],
                'pages' => $incomingFields['pages'],
                'status' => 'Draft', // Keeps or resets it to draft upon modification
                'justification' => $incomingFields['justification'],
            ]);
            ActivityLog::create([
                'action' => 'edited draft',
                'description' => 'Support Document draft has been updated.',
                'document_id' => $doc->id,
                'document_type' => 'support_document',
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()->back()->with("success","Document Updated Successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd(session()->all(), $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '.$e])->withInput();
        }
    }
}
