<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $fillable = [
        'comment', 'lead_id', 'user_id', 'Stage','due_date',
    ];

    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
