<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentFormulaAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_content_formula_endpoints(): void
    {
        $this->get(route('admin.content-formula.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.content-formula.config'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.content-formula.generate'), $this->validPayload())
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_users_are_forbidden_from_content_formula_endpoints(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get(route('admin.content-formula.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.content-formula.config'))
            ->assertForbidden();

        $this->actingAs($user)
            ->postJson(route('admin.content-formula.generate'), $this->validPayload())
            ->assertForbidden();
    }

    public function test_verified_admins_can_access_content_formula_endpoints(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.content-formula.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.content-formula.config'))
            ->assertOk();

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->validPayload())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.tier', 'paid')
            ->assertJsonPath('data.meta.requested_count', 25)
            ->assertJsonCount(5, 'data.rows.0.title_options')
            ->assertJsonCount(2, 'data.rows.0.standard_prompts')
            ->assertJsonCount(3, 'data.rows.0.optimized_prompts');
    }

    private function validPayload(): array
    {
        return [
            'action' => 'generate',
            'result_count' => 25,
            'min_words' => 600,
            'max_words' => 1200,
            'groups' => [
                'topics' => [
                    ['label' => 'Buying a Home', 'stars' => 1],
                    ['label' => 'Selling a Home', 'stars' => 1],
                    ['label' => 'First-Time Buyers', 'stars' => 1],
                    ['label' => 'Home Pricing', 'stars' => 1],
                    ['label' => 'Mortgage Options', 'stars' => 1],
                    ['label' => 'Interest Rates', 'stars' => 1],
                    ['label' => 'Closing Costs', 'stars' => 1],
                    ['label' => 'Pre-Approval', 'stars' => 1],
                    ['label' => 'Negotiation', 'stars' => 1],
                    ['label' => 'Open Houses', 'stars' => 1],
                ],
                'article_types' => [
                    ['label' => 'Problems', 'stars' => 1],
                    ['label' => 'Strategies', 'stars' => 1],
                    ['label' => 'Mistakes', 'stars' => 1],
                    ['label' => 'Trends', 'stars' => 1],
                    ['label' => 'Questions', 'stars' => 1],
                    ['label' => 'Comparisons', 'stars' => 1],
                    ['label' => 'Myths', 'stars' => 1],
                    ['label' => 'Opportunities', 'stars' => 1],
                    ['label' => 'Cases', 'stars' => 1],
                    ['label' => 'Predictions', 'stars' => 1],
                ],
                'article_formats' => [
                    ['label' => 'Checklist', 'stars' => 1],
                    ['label' => 'Guide', 'stars' => 1],
                    ['label' => 'List', 'stars' => 1],
                    ['label' => 'Steps', 'stars' => 1],
                    ['label' => 'Breakdown', 'stars' => 1],
                    ['label' => 'Explanation', 'stars' => 1],
                    ['label' => 'Framework', 'stars' => 1],
                    ['label' => 'FAQ', 'stars' => 1],
                    ['label' => 'Case Study', 'stars' => 1],
                    ['label' => 'Freeform', 'stars' => 1],
                ],
            ],
        ];
    }
}
