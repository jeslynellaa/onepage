<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => ['required','string','max:255'],
            'process_owner_id' => ['nullable','exists:users,id'],
            'reviewer_id' => ['nullable','exists:users,id'],
            'approver_id' => ['nullable','exists:users,id'],
        ]);

        $section = Section::findOrFail($id);

        $section->title = $validated['title'];
        $section->process_owner_id = $validated['process_owner_id'] ?? null;
        $section->reviewer_id = $validated['reviewer_id'] ?? null;
        $section->approver_id = $validated['approver_id'] ?? null;

        $section->save();

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.'
        ]);
    }
}
