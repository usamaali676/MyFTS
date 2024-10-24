<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = ['lead_id', 'client_nature', 'call_type', 'time_zone', 'signup_date', 'activation_date', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];


    public function closers() {
        return $this->hasMany(SaleCS::class, 'sale_id');
    }
    public function social_links(){
        return $this->hasMany(SocialLink::class, 'sale_id');
    }
    public function Customer_support()
    {
        return $this->hasMany(SaleCS::class, 'sale_id');
    }
    public function clientServices()
    {
        return $this->belongsToMany(ClientServices::class, 'client_service_sale');
    }

}
