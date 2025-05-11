<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'user_id','lead_id','source_id','status','reminder_date','reminder_time','reminder_for','feedback','phone_number','created_at'
    ];

    public function lead()
    {
        return $this->belongsTo('App\Models\Lead');
    }
}
