<?php

namespace App\Policies;

use App\Models\ClientUser;
use App\Models\MsManual;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MsManualPolicy
{
    /**
     * An FCU consultant with a live assignment to the document's company may set up
     * (create/edit) documents on the client's behalf, but never review/approve —
     * those stay gated to the client's own role-holders.
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
    public function view(User $user, MsManual $msManual): bool
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
    public function update(User $user, MsManual $msManual): bool
    {
        return
            $msManual->status != 'Active' ||
            $user->role === 'Document Controller' ||
            $user->role === 'Top Management' ||
            $this->isAssignedConsultant($user, $msManual->company_id);
    }
    
    public function sendForReview(User $user, MsManual $msManual)
    {
        return
            ($msManual->status === 'Draft' || $msManual->status === 'For Revision');
    }

    public function review(User $user, MsManual $msManual): bool
    {
        return
            $msManual->status === 'For Review';
    }

    public function approve(User $user, MsManual $msManual)
    {
        return
            $msManual->status === 'For Approval';
    }

    public function viewRevisionHistory(User $user, MsManual $msManual)
    {
        return
            $user->role === 'Document Controller';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MsManual $msManual): bool
    {
        return
            $user->role === 'Document Controller';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MsManual $msManual): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MsManual $msManual): bool
    {
        return false;
    }
}
