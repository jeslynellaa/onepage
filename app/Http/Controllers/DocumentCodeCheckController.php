<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\SupportDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Looks up a document code for the upload pages' PDF auto-fill, so they can warn when a
 * code already exists and check the PDF's revision number against the one the document
 * will get on approval. Results are company-scoped through each model's CompanyScope.
 */
class DocumentCodeCheckController extends Controller
{
    public function forms(Request $request): JsonResponse
    {
        return $this->check($request, Form::class, 'document.forms.view');
    }

    public function supportDocuments(Request $request): JsonResponse
    {
        return $this->check($request, SupportDocument::class, 'document.support_document.view');
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function check(Request $request, string $model, string $viewRoute): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'exclude' => ['nullable', 'integer'],
        ]);

        $code = trim($validated['code']);
        $query = fn () => $model::where('code', $code)
            ->when($validated['exclude'] ?? null, fn ($q, $id) => $q->where('id', '!=', $id));

        $active = $query()->where('status', 'Active')->orderByDesc('created_at')->first();
        $inProgress = $query()->whereNotIn('status', ['Active', 'Superseded'])->orderByDesc('created_at')->first();

        $summary = fn (?Model $doc) => $doc ? [
            'id' => $doc->id,
            'title' => $doc->title,
            'status' => $doc->status,
            'revision' => $doc->revision_number,
            'url' => route($viewRoute, $doc->id),
        ] : null;

        return response()->json([
            'code' => $code,
            'active' => $summary($active),
            'in_progress' => $summary($inProgress),
            // Mirrors approveOrNot: a new code starts at revision 0, a revision of the
            // Active document gets its revision number + 1.
            'expected_revision' => $active ? (int) $active->revision_number + 1 : 0,
        ]);
    }
}
