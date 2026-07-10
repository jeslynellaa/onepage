<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDistribution extends Model
{
    protected $fillable = [
        'document_id', 
        'user_id', 
        'received_at', 
        'oriented_and_retrieved_at', 
        'management_table_updated_at', 
        'updated_by_controller_id'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'oriented_and_retrieved_at' => 'datetime',
        'management_table_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function controller()
    {
        return $this->belongsTo(User::class, 'updated_by_controller_id');
    }
    
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}