<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['sale_id','status','reporting_date',
    ];

    public function sale(){
        return $this->belongsTo(Sale::class);
    }
}

