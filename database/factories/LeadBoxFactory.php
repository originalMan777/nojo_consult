<?php

namespace Database\Factories;

use App\Models\LeadBox;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LeadBox>
 */
class LeadBoxFactory extends Factory
{
    protected $model = LeadBox::class;

    public function definition(): array
    {
        $title = Str::title(fake()->words(3, true));

        return [
            'type' => LeadBox::TYPE_RESOURCE,
            'status' => LeadBox::STATUS_DRAFT,
            'internal_name' => fake()->unique()->sentence(3),
            'title' => $title,
            'short_text' => fake()->sentence(),
            'button_text' => 'Learn more',
            'icon_key' => 'book-open',
            'content' => [
                'visual_preset' => 'default',
            ],
            'settings' => [],
        ];
    }

    public function resource(): static
    {
        return $this->state(fn () => [
            'type' => LeadBox::TYPE_RESOURCE,
            'icon_key' => 'book-open',
            'content' => ['visual_preset' => 'default'],
        ]);
    }

    public function service(): static
    {
        return $this->state(fn () => [
            'type' => LeadBox::TYPE_SERVICE,
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
        ]);
    }

    public function offer(): static
    {
        return $this->state(fn () => [
            'type' => LeadBox::TYPE_OFFER,
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
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => LeadBox::STATUS_ACTIVE,
        ]);
    }
}
