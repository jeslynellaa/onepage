<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormsManualController extends Controller
{
    public function index() {
        $documents = Form::all();
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
        return view('document.forms_manual.create');
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
}
