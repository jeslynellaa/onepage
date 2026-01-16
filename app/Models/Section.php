<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;
    
    public function processOwner() {
        return $this->belongsTo(User::class, 'process_owner_id');
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
