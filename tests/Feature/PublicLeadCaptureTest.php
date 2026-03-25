<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\LeadBox;
use App\Models\LeadSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLeadCaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_lead_submission_persists_and_sets_cookie(): void
    {
        [$slot, $box] = $this->createAssignedSlot('home_intro', LeadBox::factory()->resource()->active()->create());

        $this->from(route('home'))
            ->post(route('leads.store'), [
                'lead_box_id' => $box->id,
                'lead_slot_key' => $slot->key,
                'page_key' => 'home',
                'source_url' => 'https://example.com/home',
                'first_name' => 'Jameel',
                'email' => 'lead@example.com',
            ])
            ->assertRedirect(route('home'))
            ->assertCookie('nojo_lead_captured', '1');

        $this->assertDatabaseHas('leads', [
            'lead_box_id' => $box->id,
            'lead_slot_key' => 'home_intro',
            'page_key' => 'home',
            'type' => LeadBox::TYPE_RESOURCE,
            'first_name' => 'Jameel',
            'email' => 'lead@example.com',
        ]);
    }

    public function test_service_lead_submission_requires_phone_and_persists_payload(): void
    {
        [$slot, $box] = $this->createAssignedSlot('home_mid', LeadBox::factory()->service()->active()->create());

        $this->post(route('leads.store'), [
            'lead_box_id' => $box->id,
            'lead_slot_key' => $slot->key,
            'first_name' => 'Jameel',
            'email' => 'lead@example.com',
        ])->assertSessionHasErrors('phone');

        $this->post(route('leads.store'), [
            'lead_box_id' => $box->id,
            'lead_slot_key' => $slot->key,
            'page_key' => 'home',
            'source_url' => 'https://example.com/home',
            'first_name' => 'Jameel',
            'email' => 'lead@example.com',
            'phone' => '555-1212',
            'message' => 'Need advice',
        ])->assertRedirect();

        $lead = Lead::query()->latest('id')->firstOrFail();

        $this->assertSame(LeadBox::TYPE_SERVICE, $lead->type);
        $this->assertSame('555-1212', $lead->payload['phone']);
        $this->assertSame('Need advice', $lead->payload['message']);
    }

    public function test_disabled_slots_cannot_accept_leads(): void
    {
        $slot = LeadSlot::factory()->create(['key' => 'home_intro', 'is_enabled' => false]);
        $box = LeadBox::factory()->resource()->active()->create();
        LeadAssignment::factory()->create([
            'lead_slot_id' => $slot->id,
            'lead_box_id' => $box->id,
        ]);

        $this->post(route('leads.store'), [
            'lead_box_id' => $box->id,
            'lead_slot_key' => $slot->key,
            'first_name' => 'Jameel',
            'email' => 'lead@example.com',
        ])->assertSessionHasErrors('lead_slot_key');
    }

    public function test_mismatched_assignment_is_rejected(): void
    {
        $slot = LeadSlot::factory()->create(['key' => 'home_intro', 'is_enabled' => true]);
        $assignedBox = LeadBox::factory()->resource()->active()->create();
        $wrongBox = LeadBox::factory()->resource()->active()->create();

        LeadAssignment::factory()->create([
            'lead_slot_id' => $slot->id,
            'lead_box_id' => $assignedBox->id,
        ]);

        $this->post(route('leads.store'), [
            'lead_box_id' => $wrongBox->id,
            'lead_slot_key' => $slot->key,
            'first_name' => 'Jameel',
            'email' => 'lead@example.com',
        ])->assertSessionHasErrors('lead_box_id');
    }

    public function test_inactive_lead_boxes_are_rejected_even_if_assigned(): void
    {
        [$slot, $box] = $this->createAssignedSlot('home_intro', LeadBox::factory()->resource()->create([
            'status' => LeadBox::STATUS_INACTIVE,
        ]));

        $this->post(route('leads.store'), [
            'lead_box_id' => $box->id,
            'lead_slot_key' => $slot->key,
            'first_name' => 'Jameel',
            'email' => 'lead@example.com',
        ])->assertSessionHasErrors('lead_box_id');
    }

    private function createAssignedSlot(string $key, LeadBox $box): array
    {
        $slot = LeadSlot::factory()->create([
            'key' => $key,
            'is_enabled' => true,
        ]);

        LeadAssignment::factory()->create([
            'lead_slot_id' => $slot->id,
            'lead_box_id' => $box->id,
        ]);

        return [$slot, $box];
    }
}
