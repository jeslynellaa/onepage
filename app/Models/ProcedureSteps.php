<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcedureSteps extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function document() {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function interfaces() {
        return $this->hasMany(StepDocuments::class, 'procedure_step_id');
    }

    
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
        
        static::deleting(function ($step) {
            // delete all related interfaces/documents
            $step->interfaces()->delete();
        });
    }
}
