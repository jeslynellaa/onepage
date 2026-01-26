<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StepDocuments extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function step() {
        return $this->belongsTo(ProcedureSteps::class, 'procedure_step_id');
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
