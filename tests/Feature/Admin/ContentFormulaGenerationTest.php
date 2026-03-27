<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentFormulaGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_continue_uses_same_session_and_does_not_repeat_structural_rows(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $firstResponse = $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'action' => 'generate',
                'result_count' => 25,
            ]))
            ->assertOk();

        $sessionId = $firstResponse->json('data.session.id');
        $firstSummaries = collect($firstResponse->json('data.rows'))->pluck('summary');

        $continueResponse = $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'action' => 'continue',
                'session_id' => $sessionId,
                'result_count' => 25,
            ]))
            ->assertOk();

        $continueRows = collect($continueResponse->json('data.rows'));
        $continueSummaries = $continueRows->pluck('summary');

        $this->assertSame($sessionId, $continueResponse->json('data.session.id'));
        $this->assertTrue($firstSummaries->intersect($continueSummaries)->isEmpty());
        $this->assertTrue($continueResponse->json('data.meta.can_reset'));
    }

    public function test_reset_creates_a_new_session_id_and_keeps_word_range_settings(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $firstResponse = $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'action' => 'generate',
                'min_words' => 700,
                'max_words' => 900,
            ]))
            ->assertOk();

        $newSessionResponse = $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'action' => 'reset',
                'session_id' => $firstResponse->json('data.session.id'),
                'min_words' => 700,
                'max_words' => 900,
            ]))
            ->assertOk();

        $this->assertNotSame(
            $firstResponse->json('data.session.id'),
            $newSessionResponse->json('data.session.id')
        );
        $this->assertSame(700, $newSessionResponse->json('data.meta.word_range.min'));
        $this->assertSame(900, $newSessionResponse->json('data.meta.word_range.max'));
    }

    public function test_invalid_word_ranges_are_rejected(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'min_words' => 1200,
                'max_words' => 400,
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['min_words']);
    }

    public function test_allowed_result_counts_are_accepted(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        foreach ([25, 50, 100, 150] as $count) {
            $response = $this->actingAs($admin)
                ->postJson(route('admin.content-formula.generate'), $this->payload([
                    'result_count' => $count,
                ]));

            $response->assertOk()
                ->assertJsonPath('data.meta.requested_count', $count)
                ->assertJsonCount($count, 'data.rows');
        }
    }

    public function test_unsupported_result_counts_are_rejected(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        foreach ([10, 75, 200] as $count) {
            $this->actingAs($admin)
                ->postJson(route('admin.content-formula.generate'), $this->payload([
                    'result_count' => $count,
                ]))
                ->assertStatus(422)
                ->assertJsonValidationErrors(['result_count']);
        }
    }

    public function test_generation_requires_at_least_one_thousand_combinations_for_new_sessions(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), [
                'action' => 'generate',
                'result_count' => 25,
                'min_words' => 600,
                'max_words' => 1100,
                'groups' => [
                    'topics' => [
                        ['label' => 'Buying a Home', 'stars' => 1],
                        ['label' => 'Selling a Home', 'stars' => 2],
                    ],
                    'audiences' => [
                        ['label' => 'First-Time Buyers', 'stars' => 1],
                        ['label' => 'Move-Up Buyers', 'stars' => 1],
                    ],
                ],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['groups']);
    }

    public function test_generation_requires_a_topic_selection(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), [
                'action' => 'generate',
                'result_count' => 25,
                'min_words' => 600,
                'max_words' => 1100,
                'groups' => [
                    'topics' => [],
                    'article_types' => [
                        ['label' => 'Problems', 'stars' => 1],
                    ],
                    'article_formats' => [
                        ['label' => 'Guide', 'stars' => 1],
                    ],
                    'vibes' => [
                        ['label' => 'Clear', 'stars' => 1],
                    ],
                ],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['groups.topics']);
    }

    public function test_generation_accepts_topic_plus_optional_groups_when_combination_threshold_is_met(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $topics = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Topic {$index}", 'stars' => 1])
            ->all();

        $audiences = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Audience {$index}", 'stars' => 1])
            ->all();

        $contexts = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Context {$index}", 'stars' => 1])
            ->all();

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'result_count' => 25,
                'groups' => [
                    'topics' => $topics,
                    'audiences' => $audiences,
                    'contexts' => $contexts,
                ],
            ]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.requested_count', 25)
            ->assertJsonCount(25, 'data.rows');
    }

    public function test_generation_is_accepted_when_combination_threshold_is_exactly_met(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $topics = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Topic {$index}", 'stars' => 1])
            ->all();

        $articleTypes = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Type {$index}", 'stars' => 1])
            ->all();

        $articleFormats = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Format {$index}", 'stars' => 1])
            ->all();

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), [
                'action' => 'generate',
                'result_count' => 25,
                'min_words' => 600,
                'max_words' => 1100,
                'groups' => [
                    'topics' => $topics,
                    'article_types' => $articleTypes,
                    'article_formats' => $articleFormats,
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.meta.requested_count', 25)
            ->assertJsonCount(25, 'data.rows');
    }

    public function test_generation_rejects_more_than_twenty_selections_in_a_single_group(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $topics = collect(range(1, 21))
            ->map(fn (int $index) => ['label' => "Topic {$index}", 'stars' => 1])
            ->all();

        $audiences = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Audience {$index}", 'stars' => 1])
            ->all();

        $contexts = collect(range(1, 10))
            ->map(fn (int $index) => ['label' => "Context {$index}", 'stars' => 1])
            ->all();

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'groups' => [
                    'topics' => $topics,
                    'audiences' => $audiences,
                    'contexts' => $contexts,
                ],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['groups.topics']);
    }

    public function test_generation_returns_the_requested_allowed_row_count_when_enough_combinations_exist(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.content-formula.generate'), $this->payload([
                'result_count' => 100,
            ]))
            ->assertOk()
            ->assertJsonPath('data.meta.requested_count', 100)
            ->assertJsonPath('data.meta.generated_count', 100)
            ->assertJsonCount(100, 'data.rows');
    }

    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'action' => 'generate',
            'result_count' => 25,
            'min_words' => 600,
            'max_words' => 1100,
            'groups' => [
                'topics' => [
                    ['label' => 'Buying a Home', 'stars' => 1],
                    ['label' => 'Selling a Home', 'stars' => 2],
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
                    ['label' => 'Strategies', 'stars' => 2],
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
                    ['label' => 'Guide', 'stars' => 1],
                    ['label' => 'Checklist', 'stars' => 2],
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
        ], $overrides);
    }
}
