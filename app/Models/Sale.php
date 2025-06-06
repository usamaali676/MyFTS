<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = ['lead_id', 'client_nature', 'call_type', 'time_zone', 'signup_date', 'activation_date', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'status'];


    public function closers() {
        return $this->hasMany(SaleCS::class, 'sale_id');
    }
    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id');
    }
    public function business_hours()
    {
        return $this->hasMany(BusinessHours::class, 'sale_id');
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
        return $this->belongsToMany(ClientServices::class, 'client_service_sale')
                    ->withPivot('id')  // keep track of pivot ID
                    ->withTimestamps();
    }

    public function companyServices()
    {
        return $this->hasManyThrough(
            CompanyServices::class,
            SaleClientServiceCompanyService::class,
            'sale_id', // Foreign key on the pivot table for Sale
            'id', // Local key on the company_services table
            'id', // Local key on the sales table
            'company_service_id' // Foreign key on the pivot table for Company Service
        );
    }
    public function service_area(){
        return $this->hasMany(ServiceArea::class,'sale_id');
    }
    public function keyword(){
        return $this->hasMany(Keyword::class,'sale_id');
    }
    public function invoice(){
        return $this->hasMany(Invoice::class,'sale_id');
    }

}
