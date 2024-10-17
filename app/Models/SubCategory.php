<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = ['business_category_id', 'name'];

    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class);
    }
    public function leads(){
        return $this->belongsToMany(Lead::class);
    }
}
