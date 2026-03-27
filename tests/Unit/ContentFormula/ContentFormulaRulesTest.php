<?php

namespace Tests\Unit\ContentFormula;

use App\Services\ContentFormula\ContentFormulaRules;
use Tests\TestCase;

class ContentFormulaRulesTest extends TestCase
{
    public function test_it_calculates_unlock_combinations_from_the_canonical_group_set(): void
    {
        $rules = app(ContentFormulaRules::class);

        $groups = [
            'topics' => [
                ['label' => 'Topic 1', 'stars' => 1],
                ['label' => 'Topic 2', 'stars' => 1],
                ['label' => 'Topic 3', 'stars' => 1],
                ['label' => 'Topic 4', 'stars' => 1],
                ['label' => 'Topic 5', 'stars' => 1],
            ],
            'article_types' => [
                ['label' => 'Type 1', 'stars' => 1],
                ['label' => 'Type 2', 'stars' => 1],
                ['label' => 'Type 3', 'stars' => 1],
                ['label' => 'Type 4', 'stars' => 1],
            ],
            'article_formats' => [
                ['label' => 'Format 1', 'stars' => 1],
                ['label' => 'Format 2', 'stars' => 1],
                ['label' => 'Format 3', 'stars' => 1],
                ['label' => 'Format 4', 'stars' => 1],
                ['label' => 'Format 5', 'stars' => 1],
            ],
            'vibes' => [
                ['label' => 'Vibe 1', 'stars' => 1],
                ['label' => 'Vibe 2', 'stars' => 1],
                ['label' => 'Vibe 3', 'stars' => 1],
                ['label' => 'Vibe 4', 'stars' => 1],
                ['label' => 'Vibe 5', 'stars' => 1],
                ['label' => 'Vibe 6', 'stars' => 1],
                ['label' => 'Vibe 7', 'stars' => 1],
                ['label' => 'Vibe 8', 'stars' => 1],
                ['label' => 'Vibe 9', 'stars' => 1],
                ['label' => 'Vibe 10', 'stars' => 1],
            ],
            'extra_direction' => [
                ['label' => 'Ignored', 'stars' => 1],
            ],
        ];

        $this->assertSame(1000, $rules->combinationCount($groups));
        $this->assertTrue($rules->meetsUnlockThreshold($groups));
    }
}
