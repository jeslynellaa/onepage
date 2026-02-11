<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
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
        return
            $document->status != 'Active' ||
            $user->id === $document->section->process_owner_id ||
            $user->role === 'Document Controller';
    }
    
    public function sendForReview(User $user, Document $document)
    {
        return
            $document->status === 'Draft' &&
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
