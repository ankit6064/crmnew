<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignDncCompany extends Model
{
    use HasFactory;

    protected $table = 'campaign_dnc_companies';

    protected $fillable = [
        'source_id',
        'company_name',
        'created_by',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

