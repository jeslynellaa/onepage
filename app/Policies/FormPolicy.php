<?php

namespace App\Policies;

use App\Models\ClientUser;
use App\Models\Form;
use App\Models\User;

class FormPolicy
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
    public function view(User $user, Form $form): bool
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
    public function update(User $user, Form $form): bool
    {
        return
            $form->status != 'Active' ||
            $user->id === $form->section->process_owner_id ||
            $user->role === 'Document Controller' ||
            $this->isAssignedConsultant($user, $form->company_id);
    }

    public function sendForReview(User $user, Form $form)
    {
        return
            ($form->status === 'Draft' || $form->status === 'For Revision') &&
            $user->id === $form->section->process_owner_id;
    }

    public function review(User $user, Form $form): bool
    {
        return
            $form->status === 'For Review' &&
            $user->id === $form->section->reviewer_id;
    }

    public function approve(User $user, Form $form)
    {
        return
            $form->status === 'For Approval' &&
            $user->id === $form->section->approver_id;
    }

    public function setCode(User $user, Form $form)
    {
        return
            $form->status === 'Pending Code' &&
            $user->role === 'Document Controller';
    }

    public function viewRevisionHistory(User $user, Form $form)
    {
        return
            $user->role === 'Document Controller';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Form $form): bool
    {
        return
            $user->id === $form->section->process_owner_id ||
            $user->role === 'Document Controller';
    }

    /** 
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Form $form): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Form $form): bool
    {
        return false;
    }
}
