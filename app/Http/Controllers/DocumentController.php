<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\ProcedureSteps;
use App\Models\StepDocuments;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use League\HTMLToMarkdown\HtmlConverter;

class DocumentController extends Controller
{
    public function index() {
        return view('document.index');
    }

    public function system_procedures() {
        $documents = Document::all();
        return view('document.system_procedures.index', compact('documents'));
    }

    public function sp_create() {
        return view('document.system_procedures.create');
    }

    public function sp_store(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'code' => 'required',
            'section_number' => 'required',
            'revision_number' => 'required',
            'effective_date' => 'required',
            'objective' => 'required',
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
            $newDocument = Document::create($incomingFields);

            foreach ($procedureSteps as $step) {
                // dd($step, $step['interfaces_input'], $step['interfaces_output']);
                $procedureStep = ProcedureSteps::create([
                    'document_id' => $newDocument->id,
                    'responsibility' => $step['responsibility'],
                    'activities' => $step['activities'],
                    'note' => !empty($step['note']) ? Str::markdown($step['note']) : null,
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

            DB::commit();

            return redirect()->back()->with("success","New Document Created Successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. Please try again. '.$e])->withInput();
        }
    }

    public function sp_view(Document $doc)
    {
        // Load steps and their related documents
        $doc->load(['steps.interfaces']);
        $steps = $doc->steps;

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
        $pdf = Pdf::loadView('pdf.system_procedure', compact('doc', 'steps', 'uniqueInputs', 'uniqueOutputs'))
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
            $size = 10;

            // adjust these coordinates to fit your footer area
            $canvas->text(455, 130, "Page $pageNumber of $pageCount", $font, $size);
        });

        // 5️⃣ Stream or download
        return $pdf->stream();
    }

    public function sp_edit(Document $doc) {
        $converter = new HtmlConverter();
        $doc->scope = $converter->convert($doc->scope);
        $doc->objective = $converter->convert($doc->objective);

        return view('document.system_procedures.edit', compact('doc'));
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
}
