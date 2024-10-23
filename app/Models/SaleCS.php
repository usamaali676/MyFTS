<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleCS extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'cs_id'];

    public function user(){
        return $this->belongsTo(User::class, 'cs_id');
    }
}
