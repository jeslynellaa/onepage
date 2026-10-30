<?php

namespace App\Policies;

use App\Models\ClientUser;
use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * An FCU consultant with a live assignment to the document's company may set up
     * (create/edit) documents on the client's behalf, but never review/approve/setCode —
     * those stay gated to the client's own section owner/reviewer/approver/Document Controller.
     */
    private function isAssignedConsultant(User $user, int $companyId): bool
    {
        return $user->company_id === 1
            && ClientUser::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->active()
                ->exists();
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Locked while it's with the reviewer/approver; Review not Passed / Not Approved are final.
        if (in_array($document->status, ['For Review', 'For Approval', 'Review not Passed', 'Not Approved'], true)) {
            return false;
        }

        return
            $user->id === $document->section->process_owner_id ||
            $user->role === 'Document Controller' ||
            $this->isAssignedConsultant($user, $document->company_id);
    }

    /**
     * Only the section's process owner submits — not the reviewer/approver, even while it's a Draft.
     */
    public function sendForReview(User $user, Document $document)
    {
        return
            ($document->status === 'Draft' || $document->status === 'For Revision') &&
            $user->id === $document->section->process_owner_id;
    }

    public function review(User $user, Document $document): bool
    {
        return
            $document->status === 'For Review' &&
            $user->id === $document->section->reviewer_id;
    }

    public function approve(User $user, Document $document)
    {
        return
            $document->status === 'For Approval' &&
            $user->id === $document->section->approver_id;
    }

    public function setCode(User $user, Document $document)
    {
        return
            $document->status === 'Pending Code' &&
            $user->role === 'Document Controller';
    }

    public function viewRevisionHistory(User $user, Document $document)
    {
        return
            $user->role === 'Document Controller';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return
            $user->id === $document->section->process_owner_id ||
            $user->role === 'Document Controller';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}
