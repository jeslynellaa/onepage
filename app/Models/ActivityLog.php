<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
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
        return $this->belongsTo(Document::class, 'document_id')
                ->where('document_type', 'system_procedure');
    }
    
    public function ms_manual() {
        return $this->belongsTo(MsManual::class, 'document_id')
                ->where('document_type', 'ms_manual');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function company() {
        return $this->belongsTo(Company::class);
    }
    
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}
