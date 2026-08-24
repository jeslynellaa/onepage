<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function steps() {
        return $this->hasMany(ProcedureSteps::class, 'document_id');
    }

    public function definitionOfTerms() {
        return $this->hasMany(DefinitionOfTerm::class, 'document_id');
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

    public function comments()
    {
        return $this->hasMany(ProcedureComments::class);
    }
    /**
     * Get all document records affected by this specific document's workflow.
     */
    public function affectedDocuments()
    {
        return $this->hasMany(AffectedDocument::class, 'parent_document_id');
    }

    public function distributions()
    {
        return $this->hasMany(DocumentDistribution::class, 'document_id');
    }

    public function currentUserDistribution()
    {
        return $this->hasOne(DocumentDistribution::class)->where('user_id', auth()->id());
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = CompanyContext::id();
            }
        });
        
        static::deleting(function ($document) {
            // delete all steps related to this document
            foreach ($document->steps as $step) {
                $step->delete();
            }

            $document->definitionOfTerms()->delete();
        });
    }
}
