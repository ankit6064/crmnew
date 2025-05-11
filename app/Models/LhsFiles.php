<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LhsFiles extends Model
{
    protected $fillable = [
        'lead_id',
        'file_name',
        'file_ext',
        'file_path'
    ];

    public function lhsReport(){
        return $this->belongsTo('App\Models\LhsReport', 'lhs-id');
    }

}
