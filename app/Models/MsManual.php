<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MsManual extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'effective_date' => 'datetime', 
    ];
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function logs() {
        return $this->hasMany(ActivityLog::class, 'document_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = CompanyContext::id();
            }
        });
    }
}
