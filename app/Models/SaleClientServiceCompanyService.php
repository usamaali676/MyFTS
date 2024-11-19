<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleClientServiceCompanyService extends Model
{

    // protected $table = 'sale_client_service_company_service';
    protected $fillable = ['sale_id', 'client_service_id', 'company_service_id'];

    public function clientService(){
        return $this->belongsTo(ClientServices::class, 'client_service_id');
    }

    public function companyService(){
        return $this->belongsTo(CompanyServices::class, 'company_service_id');
    }
}
