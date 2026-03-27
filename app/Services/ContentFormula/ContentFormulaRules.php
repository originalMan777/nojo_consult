<?php

namespace App\Services\ContentFormula;

class ContentFormulaRules
{
    public function allowedResultCounts(): array
    {
        return array_values(array_map(
            'intval',
            (array) config('content_formula.generator.allowed_result_counts', [25, 50, 100, 150])
        ));
    }

    public function defaultResultCount(): int
    {
        $default = (int) config('content_formula.generator.default_result_count', 50);

        return in_array($default, $this->allowedResultCounts(), true)
            ? $default
            : $this->allowedResultCounts()[0];
    }

    public function maxResultCount(): int
    {
        return max($this->allowedResultCounts());
    }

    public function normalizeResultCount(?int $resultCount): int
    {
        return in_array($resultCount, $this->allowedResultCounts(), true)
            ? (int) $resultCount
            : $this->defaultResultCount();
    }

    public function maxSelectionsPerGroup(): int
    {
        return (int) config('content_formula.generator.selection_limits.max_per_group', 20);
    }

    public function maxActiveGroups(): int
    {
        return (int) config('content_formula.generator.selection_limits.max_active_groups', 10);
    }

    public function minimumUnlockCombinations(): int
    {
        return (int) config('content_formula.generator.combination_unlock.minimum', 1000);
    }

    public function trackedCombinationGroups(): array
    {
        return array_values((array) config('content_formula.generator.combination_unlock.tracked_groups', [
            'topics',
            'article_types',
            'article_formats',
            'vibes',
            'reader_impacts',
            'audiences',
            'contexts',
            'perspectives',
        ]));
    }

    public function combinationCount(array $groups, ?array $groupKeys = null): int
    {
        $counts = collect($groupKeys ?? $this->trackedCombinationGroups())
            ->map(fn (string $groupKey) => count($groups[$groupKey] ?? []))
            ->filter(fn (int $count) => $count > 0)
            ->values();

        if ($counts->isEmpty()) {
            return 0;
        }

        return (int) $counts->reduce(
            fn (int $product, int $count) => $product * $count,
            1
        );
    }

    public function meetsUnlockThreshold(array $groups, ?array $groupKeys = null): bool
    {
        return $this->combinationCount($groups, $groupKeys) >= $this->minimumUnlockCombinations();
    }
}
