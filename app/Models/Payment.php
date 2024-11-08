<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = ['invoice_id' ,'invoice_number' ,'merchant_id' ,'mop' ,'payment_type' ,'card_number' ,'amount' ,'trans_id' ,'trans_ss', 'balance' ];
    public function merchant() {
        return $this->belongsTo(MerchantAccount::class, 'merchant_id');
    }
    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
