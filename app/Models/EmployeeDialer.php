<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDialer extends Model
{
    use HasFactory;

    protected $table = 'employee_dialers';

    protected $fillable = [
        'employee_id',
        'dialer_id',
        'dialer_password',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
