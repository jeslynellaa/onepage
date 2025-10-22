<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StepDocuments extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'procedure_step_id',
        'type',
        'category',
        'title',
    ];

    public function step() {
        return $this->belongsTo(ProcedureSteps::class, 'procedure_step_id');
    }
}
