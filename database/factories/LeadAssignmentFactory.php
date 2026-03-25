<?php

namespace Database\Factories;

use App\Models\LeadAssignment;
use App\Models\LeadBox;
use App\Models\LeadSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadAssignment>
 */
class LeadAssignmentFactory extends Factory
{
    protected $model = LeadAssignment::class;

    public function definition(): array
    {
        return [
            'lead_slot_id' => LeadSlot::factory(),
            'lead_box_id' => LeadBox::factory(),
            'override_title' => null,
            'override_short_text' => null,
            'override_button_text' => null,
        ];
    }
}
