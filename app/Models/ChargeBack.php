<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeBack extends Model
{
    protected $fillable = [
        'invoice_id',	'lead_id',	'claim_date', 'merchant_id', 'chargeBack_reason', 'payment_id'
    ];

    public function merchant() {
        return $this->belongsTo(MerchantAccount::class, 'merchant_id');
    }
    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
