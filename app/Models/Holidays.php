<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holidays extends Model
{
    // No deleted_at column on this table — holidays are hard-deleted.
    protected $fillable = ['name', 'date', 'day'];
}
