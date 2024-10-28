<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceArea extends Model
{
    use HasFactory;
    protected $fillable = ['sale_id', 'country', 'state', 'city'];
}
