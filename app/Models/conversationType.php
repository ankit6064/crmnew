<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class conversationType extends Model
{
    // use HasFactory;

    protected $table = 'conversation_types';

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',       
    ];
}
