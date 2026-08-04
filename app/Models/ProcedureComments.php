<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcedureComments extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->company_id = CompanyContext::id();
            }
        });
    }
}
