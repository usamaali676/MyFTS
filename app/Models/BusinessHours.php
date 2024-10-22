<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHours extends Model
{
    use HasFactory;
    protected $fillable = ['sale_id', 'day', 'opening_time', 'closing_time', 'is_closed', 'is_24/7',];
}
