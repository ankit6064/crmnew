<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'source_name',
        'description',
        'start_date',
        'end_date',
        'assign_to_manager',
        'assign_to_external_manager',
        'accessible_fields',
        'is_active',
    ];

    protected $dates = ['deleted_at'];


    public function leads()
    {
        return $this->hasMany(Lead::class, 'source_id');
    }
    public function closed_leads()
    {
        return $this->hasMany('App\Models\Lead')->where(['status' => 3]);
    }
    public function leadNotImported()
    {
        return $this->hasOne(LeadNotImported::class, 'source_id');
    }

}
