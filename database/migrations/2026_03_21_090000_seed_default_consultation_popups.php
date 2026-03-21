<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $rows = [
            [
                'name' => 'Popup 1',
                'slug' => 'popup-1',
                'type' => 'consultation',
                'role' => 'primary',
                'priority' => 1,
                'is_active' => true,
                'eyebrow' => 'Free Consultation',
                'headline' => 'Start with a free consultation checklist',
                'body' => 'Tell us where you are in the process and we will help you take the next right step.',
                'cta_text' => 'Get the checklist',
                'success_message' => 'Thanks. Your information was received and your next step is on the way.',
                'layout' => 'centered',
                'trigger_type' => 'time',
                'trigger_delay' => 2,
                'trigger_scroll' => null,
                'target_pages' => json_encode(['home']),
                'device' => 'all',
                'frequency' => 'once_day',
                'audience' => 'guests',
                'suppress_if_lead_captured' => true,
                'suppression_scope' => 'all_lead_popups',
                'form_fields' => json_encode(['name', 'email', 'phone']),
                'lead_type' => 'general',
                'post_submit_action' => 'message',
                'post_submit_redirect_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Popup 2',
                'slug' => 'popup-2',
                'type' => 'consultation',
                'role' => 'fallback',
                'priority' => 2,
                'is_active' => true,
                'eyebrow' => 'Still Looking?',
                'headline' => 'Need help deciding your next move?',
                'body' => 'Leave your information and we will follow up with guidance based on your situation.',
                'cta_text' => 'Request help',
                'success_message' => 'Thanks. We received your details and will follow up shortly.',
                'layout' => 'centered',
                'trigger_type' => 'time',
                'trigger_delay' => 3,
                'trigger_scroll' => null,
                'target_pages' => json_encode(['consultation', 'buyers', 'sellers']),
                'device' => 'all',
                'frequency' => 'once_day',
                'audience' => 'guests',
                'suppress_if_lead_captured' => true,
                'suppression_scope' => 'all_lead_popups',
                'form_fields' => json_encode(['name', 'email', 'phone', 'message']),
                'lead_type' => 'general',
                'post_submit_action' => 'message',
                'post_submit_redirect_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($rows as $row) {
            $existing = DB::table('popups')->where('slug', $row['slug'])->first();

            if ($existing) {
                DB::table('popups')->where('slug', $row['slug'])->update([
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'role' => $row['role'],
                    'priority' => $row['priority'],
                    'is_active' => $row['is_active'],
                    'eyebrow' => $row['eyebrow'],
                    'headline' => $row['headline'],
                    'body' => $row['body'],
                    'cta_text' => $row['cta_text'],
                    'success_message' => $row['success_message'],
                    'layout' => $row['layout'],
                    'trigger_type' => $row['trigger_type'],
                    'trigger_delay' => $row['trigger_delay'],
                    'trigger_scroll' => $row['trigger_scroll'],
                    'target_pages' => $row['target_pages'],
                    'device' => $row['device'],
                    'frequency' => $row['frequency'],
                    'audience' => $row['audience'],
                    'suppress_if_lead_captured' => $row['suppress_if_lead_captured'],
                    'suppression_scope' => $row['suppression_scope'],
                    'form_fields' => $row['form_fields'],
                    'lead_type' => $row['lead_type'],
                    'post_submit_action' => $row['post_submit_action'],
                    'post_submit_redirect_url' => $row['post_submit_redirect_url'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('popups')->insert($row);
            }
        }
    }

    public function down(): void
    {
        DB::table('popups')->whereIn('slug', ['popup-1', 'popup-2'])->delete();
    }
};
