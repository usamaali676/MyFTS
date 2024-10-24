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
        return $this->belongsToMany(Sale::class, 'client_service_sale');
    }

    public function companyServices()
    {
        return $this->hasManyThrough(CompanyServices::class, ClientServiceCompanyService::class, 'client_service_id', 'id', 'id', 'company_service_id');
    }
}
