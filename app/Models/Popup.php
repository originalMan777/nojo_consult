<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Popup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'role',
        'priority',
        'is_active',
        'eyebrow',
        'headline',
        'body',
        'cta_text',
        'success_message',
        'layout',
        'trigger_type',
        'trigger_delay',
        'trigger_scroll',
        'target_pages',
        'device',
        'frequency',
        'audience',
        'suppress_if_lead_captured',
        'suppression_scope',
        'form_fields',
        'lead_type',
        'post_submit_action',
        'post_submit_redirect_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'suppress_if_lead_captured' => 'boolean',
        'target_pages' => 'array',
        'form_fields' => 'array',
    ];
}
