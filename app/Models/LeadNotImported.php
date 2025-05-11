<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadNotImported extends Model
{
    // Specify the table associated with the model
    protected $table = 'leads_not_imported';

    // Specify the primary key (if different from 'id')
    protected $primaryKey = 'id';

    // Specify if the primary key is auto-incrementing (default is true)
    public $incrementing = true;

    // Specify the type of the primary key (default is 'int')
    protected $keyType = 'int';

    // Specify whether the model should be timestamped (created_at, updated_at)
    public $timestamps = true;

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'source_id',
        'file_name'
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}

