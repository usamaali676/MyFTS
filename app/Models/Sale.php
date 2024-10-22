<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = ['lead_id', 'client_nature', 'call_type', 'time_zone', 'signup_date', 'activation_date', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
}
