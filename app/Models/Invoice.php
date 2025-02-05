<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['sale_id' ,'invoice_number' ,'discount_type' ,'discount_amount' ,'invoice_due_date' ,'invoice_frequency' ,'total_amount' ,'marchent_id' ,'invoice_active_status' ,'activation_date' ,'mop', 'month', 'created_by' ];

    public function marchent() {
        return $this->belongsTo(MerchantAccount::class, 'marchent_id');
    }
    public function sale() {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
    public function servicecharges(){
        return $this->hasMany(InvoiceServiceCharges::class, 'invoice_id');
    }
    public function payments(){
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
