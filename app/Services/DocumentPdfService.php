<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentPdfService
{
    public function generate($doc)
    {
        $doc->load(['steps.interfaces', 'section.processOwner', 'section.reviewer', 'section.approver']);
        $steps = $doc->steps;
        
        $submitted = $doc->logs->firstWhere('action', 'submitted for review');
        $passed = $doc->logs->firstWhere('action', 'review passed');
        $approved = $doc->logs->firstWhere('action', 'approved');
        
        $color = $doc->company->hex_code;
        $text_color = $this->getTextColorForBackground($color);
        $font = $doc->company->pdf_font;

        // Path Logic

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
        
        // ... Load other signatures similarly ...


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
        $pdf = Pdf::loadView('pdf.system_procedure', compact('doc', 'steps', 'uniqueInputs', 'uniqueOutputs', 'connector', 'submitted', 'passed', 'approved', 'owner_sign', 'reviewer_sign', 'approver_sign', 'logo', 'color', 'text_color', 'font'))
                ->setPaper($doc->company->paper_size, 'portrait');

        // 3. Render and Page Counting
        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        
        // Update total pages in DB
        $doc->update(['pages' => $canvas->get_page_count()]);

        // 4. Page Numbering Script
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font) {
            $selectedFont = $fontMetrics->get_font($font, "normal");
            $x = 455;
            $y = 111;
            if($font == 'Times-Roman'){
                $x = 467;
            }
            $canvas->text($x, $y, "Page $pageNumber of $pageCount", $selectedFont, 11);
        });

        return $pdf;
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
}