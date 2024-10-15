<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = [ 'category_id', 'saler_id', 'business_name_adv', 'business_number_adv',	'off_email', 'website_url' , 	'lead_status', 'call_status', 'call_back_time',
    ];

    public function category() {
        return $this->belongsTo(BusinessCategory::class, 'category_id');
    }
    public function saler() {
        return $this->belongsTo(User::class, 'saler_id');
    }
    public function sub_categories(){
        return $this->belongsToMany(SubCategory::class);
    }
}
