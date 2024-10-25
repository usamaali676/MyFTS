<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleClientServiceCompanyService extends Model
{
    protected $fillable = ['sale_id', 'client_service_id', 'company_service_id'];
}
