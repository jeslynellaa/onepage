<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function steps() {
        return $this->hasMany(ProcedureSteps::class, 'document_id');
    }

    public function logs() {
        return $this->hasMany(ActivityLog::class, 'document_id');
    }

    public function dirf() {
        return $this->hasOne(Dirf::class, 'document_id');
    }
    
    public function section() {
        return $this->belongsTo(Section::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
        
        static::deleting(function ($document) {
            // delete all steps related to this document
            foreach ($document->steps as $step) {
                $step->delete();
            }
        });
    }
}
