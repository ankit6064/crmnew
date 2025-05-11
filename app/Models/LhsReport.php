<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class LhsReport extends Model
{
    use SoftDeletes;
    public $table = "lhs_report";
    protected $fillable = [

        'lead_id',
        'board_no',
        'direct_no',
        'employees_strength',
        'revenue',
        'address',
        'website',
        'prospect_vertical',
        'prospects_level',
        'opt_in_status',
        'company_desc',
        'responsibilities',
        'team_size',
        'pain_areas',
        'interest_new_initiatives',
        'budget',
        'defined_agenda',
        'call_notes',
        'meeting_date1',
        'meeting_date2',
        'meeting_time1',
        'meeting_time2',
        'meeting_teleconference',
        'contact_decision_maker',
        'influencers_decision_making_process',
        'company_already_affiliated',
        'ext_if_any',
        'ea_name',
        'ea_email',
        'ea_phone_no',
        'timezone_1',
        'timezone_2',

    ];
    

    public function lhsFiles(){
        return $this->hasOne('App\Models\LhsFiles', 'lhs-id');
    }
    
}
