<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ProcedureSteps::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function step_documents(): HasMany
    {
        return $this->hasMany(StepDocuments::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_status !== 'active') {
            return false;
        }

        if ($this->subscription_ends_at && now()->gt($this->subscription_ends_at)) {
            return false;
        }

        return true;
    }
}
