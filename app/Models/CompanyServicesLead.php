<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyServicesLead extends Model
{
    protected $table = 'company_services_lead';
    //
    protected $fillable = ['company_services_id', 'lead_id'];
}
