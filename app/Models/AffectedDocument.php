<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffectedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_document_id',
        'affected_document_id',
        'title',
        'code',
        'revision_number',
        'details',
    ];

    protected static function booted()
    {
        // Apply your multi-tenancy scoping safely
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    /**
     * The document that initiated the revision request.
     */
    public function parentDocument()
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    /**
     * The document that is impacted by this change.
     */
    public function affectedDocument()
    {
        return $this->belongsTo(Document::class, 'affected_document_id');
    }
}