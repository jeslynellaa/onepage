<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        static::deleting(function ($document) {
            // delete all steps related to this document
            foreach ($document->steps as $step) {
                $step->delete();
            }
        });
    }
}
