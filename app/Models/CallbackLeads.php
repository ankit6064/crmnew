<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallbackLeads extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'employee_id',
        'lead_id',
        'callback_date',
        'callback_time',
    ];
}
