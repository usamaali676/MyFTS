<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyServices extends Model
{
    use HasFactory;

    public function clientServicesForSale($saleId)
    {
        return $this->belongsToMany(ClientServices::class, 'sale_client_service_company_services', 'company_service_id', 'client_service_id')
                    ->wherePivot('sale_id', $saleId);
    }

}
