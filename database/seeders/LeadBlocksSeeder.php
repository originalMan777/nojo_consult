<?php

namespace Database\Seeders;

use App\Models\LeadBox;
use App\Models\LeadSlot;
use Illuminate\Database\Seeder;

class LeadBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $slotKeys = config('lead_blocks.slot_keys', ['home_intro', 'home_mid', 'home_bottom']);

        foreach ($slotKeys as $slotKey) {
            if (! is_string($slotKey) || $slotKey === '') {
                continue;
            }

            LeadSlot::query()->updateOrCreate(
                ['key' => $slotKey],
                ['is_enabled' => true],
            );
        }

        LeadBox::query()->firstOrCreate(
            ['internal_name' => 'Default Resource Lead Box'],
            [
                'type' => LeadBox::TYPE_RESOURCE,
                'status' => LeadBox::STATUS_DRAFT,
                'title' => 'Get the Home Buyer Checklist',
                'short_text' => 'A quick, practical checklist to help you prepare and avoid common mistakes.',
                'button_text' => 'Get the checklist',
                'icon_key' => 'book-open',
                'content' => [
                    'visual_preset' => 'default',
                ],
                'settings' => [],
            ],
        );

        LeadBox::query()->firstOrCreate(
            ['internal_name' => 'Default Service Lead Box'],
            [
                'type' => LeadBox::TYPE_SERVICE,
                'status' => LeadBox::STATUS_DRAFT,
                'title' => 'Talk through your next step',
                'short_text' => 'We’ll help you understand options, timing, and a plan that fits your goals.',
                'button_text' => 'Request a call',
                'icon_key' => null,
                'content' => [
                    'cta_line' => 'Quick question? Let’s get you answers.',
                    'reassurance_text' => 'No pressure. No spam.',
                    'value_points' => [
                        ['icon_key' => 'shield-check', 'line' => 'Clear guidance'],
                        ['icon_key' => 'clock', 'line' => 'Fast response'],
                        ['icon_key' => 'message-square', 'line' => 'Practical next steps'],
                    ],
                ],
                'settings' => [],
            ],
        );

        LeadBox::query()->firstOrCreate(
            ['internal_name' => 'Default Offer Lead Box'],
            [
                'type' => LeadBox::TYPE_OFFER,
                'status' => LeadBox::STATUS_DRAFT,
                'title' => 'Limited: Strategy Session Package',
                'short_text' => 'A focused 45‑minute call to map your best next move.',
                'button_text' => 'Claim your spot',
                'icon_key' => null,
                'content' => [
                    'breakdown_line_2' => 'Perfect if you need clarity, fast.',
                    'cta_line' => 'Ready for a confident plan?',
                    'reassurance_text' => 'No obligation. Just clarity.',
                    'value_points' => [
                        ['icon_key' => 'sparkles', 'line' => 'Personalized strategy'],
                        ['icon_key' => 'check-circle-2', 'line' => 'Clear next steps'],
                        ['icon_key' => 'clock', 'line' => 'Fast turnaround'],
                    ],
                ],
                'settings' => [],
            ],
        );
    }
}
