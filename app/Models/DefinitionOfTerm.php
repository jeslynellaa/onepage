<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefinitionOfTerm extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
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
