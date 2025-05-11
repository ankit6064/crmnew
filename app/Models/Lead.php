<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'user_id',
        'source_id',
        'company_name',
        'prospect_first_name',
        'prospect_last_name',
        'location',
        'timezone',
        'company_industry',
        'prospect_name',
        'designation',
        'linkedin_address',
        'asign_to_manager',
        'bussiness_function',
        'prospect_email',
        'contact_number_1',
        'contact_number_2',
        'designation_level',
        'date_shared',
        'is_notify',
        'is_read',
        'assign_to_external_manager',
        'approval_status',
        'asign_to'
    ];

    public function source()
    {
        return $this->belongsTo('App\Models\Source');
    }
    public function lhsreport()
    {
        return $this->hasOne('App\Models\LhsReport', 'lead_id', 'id');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\Installment');
    }

    public function notes()
    {
        return $this->hasMany('App\Models\Note')->orderBy('created_at', 'DESC');
    }
    public function note()
    {
        return $this->hasOne('App\Models\Note');
    }

    public function feedback()
    {
        return $this->hasOne('App\Models\Feedback');
    }

    public function amount_received()
    {
        return $this->hasMany('App\Models\Installment')->where(['status' => 2]);
    }

    public function userWithTrashed()
    {
        return $this->belongsTo('App\Models\User', 'asign_to')->withTrashed();
    }

    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'asign_to');
    }

    public function leadManager()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function momReport()
    {
        return $this->hasOne(MomReport::class, 'lead_id');
    }

    public function managerAssigned()
    {
        return $this->belongsTo(User::class, 'asign_to_manager');
    }
}