<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        "lead_id",
        "user_id",
        "type",
        "message",
        "is_read",
        "created_at",
        "updated_at"
    ];
}
