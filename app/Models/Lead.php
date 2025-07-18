<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [ 'saler_id', 'business_name_adv', 'business_number_adv',	'off_email', 'website_url' , 	'lead_status', 'call_status', 'call_back_time', 'created_by', 'updated_by', 'deleted_by' , 'client_name', 'client_address', 'client_designation',  'additional_number', 'additional_email', 'country', 'state', 'city', 'zip_code'
    ];

    public function category() {
        return $this->belongsToMany(BusinessCategory::class);
    }
    public function saler() {
        return $this->belongsTo(User::class, 'saler_id');
    }
    public function sub_categories(){
        return $this->belongsToMany(SubCategory::class);
    }
    public function closers() {
        return $this->hasMany(LeadCloser::class, 'lead_id');
    }
    public function sale(){
        return $this->hasOne(Sale::class, 'lead_id');
    }
    public function company_services()
    {
        return $this->belongsToMany(CompanyServices::class, );
    }
    public function comment()
    {
        return $this->hasMany(Comments::class, 'lead_id');
    }
    public function additional_info()
    {
        return $this->hasMany(LeadAdditionalInfo::class, 'lead_id');
    }
    public function chargeback(){
        return $this->hasOne(ChargeBack::class, 'lead_id');
    }
}
