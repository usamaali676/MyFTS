<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientServices extends Model
{
    use HasFactory;
    protected $fillable = ['name',];


        public function sales()
    {
        return $this->belongsToMany(Sale::class, 'client_service_sale')
                    ->withPivot('id')
                    ->withTimestamps();
    }

    public function saleSpecificCompanyServices()
    {
        return $this->hasMany(SaleClientServiceCompanyService::class);
    }

    public function companyServices()
    {
        return $this->hasManyThrough(CompanyServices::class, ClientServiceCompanyService::class, 'client_service_id', 'id', 'id', 'company_service_id');
    }
    public function companyServicesForSale($saleId)
{
    return $this->belongsToMany(CompanyServices::class, 'sale_client_service_company_services', 'client_service_id', 'company_service_id')
                ->wherePivot('sale_id', $saleId);
}
}
