<?php

namespace Database\Factories;

use App\Models\Popup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Popup>
 */
class PopupFactory extends Factory
{
    protected $model = Popup::class;

    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => 'general',
            'role' => 'standard',
            'priority' => 100,
            'is_active' => true,
            'eyebrow' => fake()->optional()->words(2, true),
            'headline' => fake()->sentence(),
            'body' => fake()->optional()->paragraph(),
            'cta_text' => 'Submit',
            'success_message' => 'Thanks. We received your information.',
            'layout' => 'centered',
            'trigger_type' => 'time',
            'trigger_delay' => 2,
            'trigger_scroll' => null,
            'target_pages' => ['home'],
            'device' => 'all',
            'frequency' => 'once_day',
            'audience' => 'guests',
            'suppress_if_lead_captured' => true,
            'suppression_scope' => 'all_lead_popups',
            'form_fields' => ['name', 'email'],
            'lead_type' => 'general',
            'post_submit_action' => 'message',
            'post_submit_redirect_url' => null,
        ];
    }
}
