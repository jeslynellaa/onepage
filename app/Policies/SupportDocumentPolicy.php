<?php

namespace App\Policies;

use App\Models\ClientUser;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupportDocumentPolicy
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
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupportDocument $supportDocument): bool
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
    public function update(User $user, SupportDocument $supportDocument): bool
    {
        return
            $supportDocument->status != 'Active' ||
            $user->id === $supportDocument->section->process_owner_id ||
            $user->role === 'Document Controller' ||
            $this->isAssignedConsultant($user, $supportDocument->company_id);
    }
    
    public function sendForReview(User $user, SupportDocument $supportDocument)
    {
        return
            ($supportDocument->status === 'Draft' || $supportDocument->status === 'For Revision') &&
            $user->id === $supportDocument->section->process_owner_id;
    }

    public function review(User $user, SupportDocument $supportDocument): bool
    {
        return
            $supportDocument->status === 'For Review' &&
            $user->id === $supportDocument->section->reviewer_id;
    }

    public function approve(User $user, SupportDocument $supportDocument)
    {
        return
            $supportDocument->status === 'For Approval' &&
            $user->id === $supportDocument->section->approver_id;
    }

    public function setCode(User $user, SupportDocument $supportDocument)
    {
        return
            $supportDocument->status === 'Pending Code' &&
            $user->role === 'Document Controller';
    }

    public function viewRevisionHistory(User $user, SupportDocument $supportDocument)
    {
        return
            $user->role === 'Document Controller';
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupportDocument $supportDocument): bool
    {
        return
            $user->id === $supportDocument->section->process_owner_id ||
            $user->role === 'Document Controller';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupportDocument $supportDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupportDocument $supportDocument): bool
    {
        return false;
    }
}
