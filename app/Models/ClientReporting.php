<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReporting extends Model
{
        protected $table = 'client_reportings';

    protected $fillable = [ 'client_id' , 'report_type' , 'report_month', 'report_year', 'uuid',  'created_by' , 'verified_by' , 'dispatched_by' , 'report_file' , 'report_status' , 'report_verified_at' , 'dispatch_at' ,
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
    public function website()
    {
        return $this->hasOne(WebsiteReportDetail::class);
    }

    public function landingPage()
    {
        return $this->hasOne(LandingPageReportDetail::class, 'client_reporting_id');
    }

    public function gmb()
    {
        return $this->hasOne(GmbReportDetail::class);
    }
}
