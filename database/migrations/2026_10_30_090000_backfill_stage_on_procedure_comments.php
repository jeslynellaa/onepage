<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * System Procedure comments were saved without a stage, so the document view (which groups them
 * into Review / Approval panels by stage) never showed them. Infer it from the commenter's role on
 * the document's section: the section approver's comments are approval-stage, the rest review-stage.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('procedure_comments')
            ->leftJoin('documents', 'documents.id', '=', 'procedure_comments.document_id')
            ->leftJoin('sections', 'sections.id', '=', 'documents.section_id')
            ->whereNull('procedure_comments.stage')
            ->select('procedure_comments.id', 'procedure_comments.user_id', 'sections.reviewer_id', 'sections.approver_id')
            ->orderBy('procedure_comments.id')
            ->get()
            ->each(function ($comment) {
                $isApprover = $comment->user_id == $comment->approver_id && $comment->user_id != $comment->reviewer_id;

                DB::table('procedure_comments')
                    ->where('id', $comment->id)
                    ->update(['stage' => $isApprover ? 'approval' : 'review']);
            });
    }

    public function down(): void
    {
        // Data backfill only; the inferred stages are not reversible.
    }
};
