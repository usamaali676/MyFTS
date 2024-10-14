<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['business_category_id', 'name'];

    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class);
    }
}
