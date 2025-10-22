<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcedureSteps extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function document() {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function interfaces() {
        return $this->hasMany(StepDocuments::class, 'procedure_step_id');
    }

    
    protected static function booted()
    {
        static::deleting(function ($step) {
            // delete all related interfaces/documents
            $step->interfaces()->delete();
        });
    }
}
