<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientServiceCompanyService extends Model
{
    use HasFactory;

    protected $table = 'client_service_company_services';
    protected $fillable = ['client_service_id', 'company_service_id'];

    public function clientService()
    {
        return $this->belongsTo(ClientServices::class, 'client_service_id');
    }

    public function companyService()
    {
        return $this->belongsTo(CompanyServices::class, 'company_service_id');
    }
}
