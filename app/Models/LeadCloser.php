<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCloser extends Model
{
    use HasFactory;
    protected $fillable = ['lead_id', 'closer_id', 'closed_at'];

    public function user(){
        return $this->belongsTo(User::class, 'closer_id');
    }
}
