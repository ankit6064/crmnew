<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Money extends Model
{
    protected $table = 'money';

    protected $fillable = [
        'source_id','amount','date',
    ];


     //public function user(){
     //   return $this->belongsTo(User::class);
    //}
    public function source(){
        return $this->belongsTo('App\Models\Source');
    }
}
