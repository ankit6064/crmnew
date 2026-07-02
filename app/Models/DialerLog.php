<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialerLog extends Model
{
    public $timestamps = true;
    protected $table = 'dialer_logs';
    protected $fillable = [
        'employee_id',
        'lead_id',
        'phone_number',
        'event'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
