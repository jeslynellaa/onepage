<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dirf extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function document() {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
