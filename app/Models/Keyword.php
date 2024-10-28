<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = ['sale_id','area_id', 'keyword','primary', 'secondary'];

    public function area()
    {
        return $this->belongsTo(ServiceArea::class);
    }
}
