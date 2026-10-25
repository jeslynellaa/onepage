<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_demo' => 'boolean',
        'demo_expires_at' => 'datetime',
    ];

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
    
    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }

    public function consultants()
    {
        return $this->belongsToMany(User::class, 'client_users')
            ->wherePivot('status', 'active')
            ->withPivot(['status', 'revoked_at', 'assigned_by_user_id'])
            ->withTimestamps();
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

    /**
     * A throwaway demo company whose time is up. Expired demos are locked out
     * immediately (see DemoGuard) and deleted by the hourly demo:prune run.
     */
    public function demoExpired(): bool
    {
        return $this->is_demo
            && $this->demo_expires_at
            && now()->gte($this->demo_expires_at);
    }

    protected static function booted()
    {
        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = static::generateUniqueSlug($company->name);
            }
        });

        static::updating(function ($company) {
            if ($company->isDirty('name')) {
                $company->slug = static::generateUniqueSlug(
                    $company->name,
                    $company->id
                );
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        do {
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        } while (true);

        return $slug;
    }

}
