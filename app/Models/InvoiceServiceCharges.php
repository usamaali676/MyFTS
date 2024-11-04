<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceServiceCharges extends Model
{
    //
    protected $fillable = ['invoice_id' ,'company_service_id' ,'shelf_amount' ,'charged_price' ,'discount_price' ,'is_complementary' ];

    public function service_name(){
        return $this->belongsTo(CompanyServices::class, 'company_service_id');
    }
}
