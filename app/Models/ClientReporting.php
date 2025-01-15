<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReporting extends Model
{
    protected $fillable = [ 'client_id' , 'reporting_type' , 'created_by' , 'verified_by' , 'dispatched_by' , 'report_file' , 'report_status' , 'report_verified_at' , 'dispatch_at' ,
    ];

    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function verifiedBy(){
        return $this->belongsTo(User::class,'verified_by');
    }
    public function dispatchedBy(){
        return $this->belongsTo(User::class, 'dispatched_by');
    }
}
