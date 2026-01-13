<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'performed_at' => 'datetime',
    ];
    
    public function document() {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
