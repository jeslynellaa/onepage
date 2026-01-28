<?php

namespace App\Models;

use Illuminate\Support\Str;
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
