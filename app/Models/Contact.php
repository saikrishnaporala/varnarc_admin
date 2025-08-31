<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'email', 'score', 'phone', 'companyname', 'tags', 
        'mobile', 'leadsource', 'home_phone', 'dept', 'fax', 'dob', 'assistant',
        'reportsto', 'assistant_phone', 'sec_email', 'emailopt', 'donotcall',
        'reference', 'assignedto', 'notifyowner', 'portaluser', 'supportstartdate',
        'supportenddate', 'm_street', 'm_city', 'm_state', 'm_zip', 'm_country',
        'o_street', 'o_city', 'o_state', 'o_zip', 'o_country', 'description',
        'createdby', 'createdip', 'modifiedby', 'modifiedip'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

}
