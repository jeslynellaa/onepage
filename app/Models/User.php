<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['username', 'first_name', 'middle_name', 'last_name', 'role', 'active', 'email', 'password', 'signature_path'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function logs() {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
    
    public function fullname()
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name : '';
        return "{$this->last_name}, {$this->first_name}{$middle}";
    }
    
    public function ownedSections() {
        return $this->hasMany(Section::class, 'process_owner_id');
    }

    public function reviewedSections() {
        return $this->hasMany(Section::class, 'reviewer_id');
    }

    public function approvedSections() {
        return $this->hasMany(Section::class, 'approver_id');
    }
}
