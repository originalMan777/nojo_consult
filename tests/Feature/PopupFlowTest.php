<?php

namespace Tests\Feature;

use App\Models\Popup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PopupFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_popup_submit_route_is_registered_and_active_popup_submissions_persist(): void
    {
        $popup = Popup::factory()->create([
            'slug' => 'welcome-popup',
            'form_fields' => ['name', 'email', 'phone'],
            'suppression_scope' => 'all_lead_popups',
        ]);

        $this->from(route('home'))
            ->post(route('popup-leads.store'), [
                'popup_id' => $popup->id,
                'page_key' => 'home',
                'source_url' => 'https://example.com/',
                'name' => 'Jameel',
                'email' => 'popup@example.com',
                'phone' => '555-1212',
            ])
            ->assertRedirect(route('home'))
            ->assertCookie('nojo_popup_submitted_welcome_popup', '1')
            ->assertCookie('nojo_lead_captured', '1');

        $this->assertDatabaseHas('popup_leads', [
            'popup_id' => $popup->id,
            'page_key' => 'home',
            'email' => 'popup@example.com',
            'phone' => '555-1212',
        ]);
    }

    public function test_inactive_popup_submissions_are_not_accepted(): void
    {
        $popup = Popup::factory()->create(['is_active' => false]);

        $this->post(route('popup-leads.store'), [
            'popup_id' => $popup->id,
            'email' => 'popup@example.com',
        ])->assertNotFound();
    }

    public function test_popup_manager_only_shares_popups_for_the_current_page_and_audience(): void
    {
        Popup::factory()->create([
            'name' => 'Home guest popup',
            'slug' => 'home-guest-popup',
            'target_pages' => ['home'],
            'audience' => 'guests',
        ]);

        Popup::factory()->create([
            'name' => 'Authenticated popup',
            'slug' => 'auth-popup',
            'target_pages' => ['home'],
            'audience' => 'authenticated',
        ]);

        Popup::factory()->create([
            'name' => 'Contact popup',
            'slug' => 'contact-popup',
            'target_pages' => ['contact'],
            'audience' => 'guests',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->where('popupManager.pageKey', 'home')
                ->where('popupManager.popups', function ($popups): bool {
                    $slugs = collect($popups)->pluck('slug');

                    return $slugs->contains('home-guest-popup')
                        && ! $slugs->contains('auth-popup')
                        && ! $slugs->contains('contact-popup');
                })
            );
    }

    public function test_popup_manager_suppresses_lead_capture_and_popup_specific_cookies(): void
    {
        $allLeadPopup = Popup::factory()->create([
            'slug' => 'all-lead-popup',
            'target_pages' => ['home'],
            'suppression_scope' => 'all_lead_popups',
            'suppress_if_lead_captured' => true,
        ]);

        $singlePopup = Popup::factory()->create([
            'slug' => 'single-popup',
            'target_pages' => ['home'],
            'suppression_scope' => 'this_popup_only',
            'suppress_if_lead_captured' => false,
        ]);

        $this->withCookie('nojo_lead_captured', '1')
            ->withCookie('nojo_popup_submitted_single_popup', '1')
            ->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->where('popupManager.leadCaptured', true)
                ->has('popupManager.popups', 0)
            );

        $this->assertDatabaseHas('popups', ['id' => $allLeadPopup->id]);
        $this->assertDatabaseHas('popups', ['id' => $singlePopup->id]);
    }

    public function test_admin_popup_routes_remain_admin_only(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.popups.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.popups.store'), [
            'name' => 'Blocked popup',
            'type' => 'general',
            'role' => 'standard',
            'priority' => 10,
            'headline' => 'Blocked',
            'cta_text' => 'Submit',
            'layout' => 'centered',
            'trigger_type' => 'time',
            'device' => 'all',
            'frequency' => 'once_day',
            'audience' => 'guests',
            'suppression_scope' => 'all_lead_popups',
            'lead_type' => 'general',
            'post_submit_action' => 'message',
        ])->assertForbidden();
    }
}
