<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignVisit extends Model
{
    protected $fillable = [
        'campaign_name',
        'tenant_id',
        'ip_address',
        'user_agent',
        'referer',
    ];
}
