<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupLead extends Model
{
    protected $fillable = [
        'popup_id',
        'page_key',
        'source_url',
        'lead_type',
        'name',
        'email',
        'phone',
        'message',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
