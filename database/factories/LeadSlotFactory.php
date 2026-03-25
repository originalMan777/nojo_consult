<?php

namespace Database\Factories;

use App\Models\LeadSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadSlot>
 */
class LeadSlotFactory extends Factory
{
    protected $model = LeadSlot::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'is_enabled' => true,
        ];
    }
}
