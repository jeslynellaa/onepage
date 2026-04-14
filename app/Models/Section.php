<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;
    
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }

    public function support_documents() {
        return $this->hasMany(SupportDocument::class);
    }

    public function forms() {
        return $this->hasMany(Form::class);
    }
    
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
