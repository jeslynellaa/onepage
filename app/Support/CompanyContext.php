<?php

namespace App\Support;

use App\Models\ClientUser;
use App\Models\Company;

class CompanyContext
{
    /**
     * The company_id that scoped queries/writes should use for the current request:
     * the consultant's actively-entered client company if one is validly assigned,
     * otherwise the user's own company.
     */
    public static function id(): ?int
    {
        if (! auth()->check()) {
            return null;
        }

        $activeClientId = session('active_client_id');

        if ($activeClientId && static::hasActiveAssignment(auth()->id(), $activeClientId)) {
            return (int) $activeClientId;
        }

        return auth()->user()->company_id;
    }

    public static function isConsultantMode(): bool
    {
        if (! auth()->check() || ! session()->has('active_client_id')) {
            return false;
        }

        return static::id() !== auth()->user()->company_id;
    }

    public static function activeClient(): ?Company
    {
        return static::isConsultantMode() ? Company::find(session('active_client_id')) : null;
    }

    public static function hasActiveAssignment(int $userId, int $companyId): bool
    {
        return ClientUser::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->active()
            ->exists();
    }
}
