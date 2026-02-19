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
        
        return view('document.ms_manual.index', compact('ms_manuals'));
    }

    public function create()
    {
        return view('document.ms_manual.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
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

    public function view(MsManual $doc)
    {
        return view('document.ms_manual.view', compact('doc'));
    }
}
