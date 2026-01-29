<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['expires_at', 'used_at'];

    public function isExpired()
    {
        return now()->gt($this->expires_at);
    }

    public function isUsed()
    {
        return !is_null($this->used_at);
    }
    
    public function company() {
        return $this->belongsTo(Company::class);
    }
    
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}
