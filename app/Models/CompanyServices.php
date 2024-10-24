<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyServices extends Model
{
    use HasFactory;

    public function clientServices()
    {
        return $this->belongsTo(ClientServices::class, 'client_service_company_services');
    }

}
