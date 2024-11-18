<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAdditionalInfo extends Model
{
    //
    protected $fillable = ['lead_id', 'name', 'value'];
}
