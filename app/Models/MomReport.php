<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MomReport extends Model
{
    use SoftDeletes;

    protected $table = "mom_report";

    protected $fillable = [
        'lead_id',
        'meeting_datetime',
        'time_zone',
        'account',
        'bdm_id',
        'setup_by_id',
        'company_name',
        'exl_participants',
        'customer_participants_and_designations',
        'meeting_notes',
        'additional_notes',
        'actions',
        'owners_id',
        'due_by'
    ];
}
