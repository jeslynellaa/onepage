<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['expires_at', 'used_at'];

    public function isExpired()
    {
        return now()->gt($this->expires_at);
    }

    public function isUsed()
    {
        return !is_null($this->used_at);
    }
    
    public function company() {
        return $this->belongsTo(Company::class);
    }
}
