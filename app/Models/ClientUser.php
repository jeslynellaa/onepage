<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    protected $guarded = [];

    protected $casts = [
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
