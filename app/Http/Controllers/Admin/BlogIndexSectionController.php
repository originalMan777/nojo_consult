<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogIndexSection;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BlogIndexSectionController extends Controller
{
    public function index()
    {
        $sections = $this->ensureSections()->keyBy('section_key');

        return Inertia::render('Admin/BlogIndexSections/Index', [
            'sections' => [
                BlogIndexSection::KEY_WIDE => $this->mapSection($sections->get(BlogIndexSection::KEY_WIDE)),
                BlogIndexSection::KEY_CLUSTER_LEFT => $this->mapSection($sections->get(BlogIndexSection::KEY_CLUSTER_LEFT)),
                BlogIndexSection::KEY_CLUSTER_RIGHT => $this->mapSection($sections->get(BlogIndexSection::KEY_CLUSTER_RIGHT)),
            ],
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'sourceOptions' => [
                ['value' => BlogIndexSection::SOURCE_LATEST, 'label' => 'Latest'],
                ['value' => BlogIndexSection::SOURCE_FEATURED, 'label' => 'Featured'],
                ['value' => BlogIndexSection::SOURCE_CATEGORY, 'label' => 'Category'],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.wide_section.enabled' => ['required', 'boolean'],
            'sections.wide_section.source_type' => ['required', 'string', Rule::in(BlogIndexSection::SOURCE_TYPES)],
            'sections.wide_section.category_id' => ['nullable', 'integer', 'exists:categories,id', 'required_if:sections.wide_section.source_type,category'],
            'sections.wide_section.title_override' => ['nullable', 'string', 'max:255'],
            'sections.cluster_left.enabled' => ['required', 'boolean'],
            'sections.cluster_left.source_type' => ['required', 'string', Rule::in(BlogIndexSection::SOURCE_TYPES)],
            'sections.cluster_left.category_id' => ['nullable', 'integer', 'exists:categories,id', 'required_if:sections.cluster_left.source_type,category'],
            'sections.cluster_left.title_override' => ['nullable', 'string', 'max:255'],
            'sections.cluster_right.enabled' => ['required', 'boolean'],
            'sections.cluster_right.source_type' => ['required', 'string', Rule::in(BlogIndexSection::SOURCE_TYPES)],
            'sections.cluster_right.category_id' => ['nullable', 'integer', 'exists:categories,id', 'required_if:sections.cluster_right.source_type,category'],
            'sections.cluster_right.title_override' => ['nullable', 'string', 'max:255'],
        ]);

        $sections = $this->ensureSections()->keyBy('section_key');

        foreach (BlogIndexSection::SECTION_KEYS as $sectionKey) {
            $payload = $validated['sections'][$sectionKey] ?? [];
            $sourceType = (string) ($payload['source_type'] ?? BlogIndexSection::SOURCE_LATEST);

            $sections[$sectionKey]->update([
                'enabled' => (bool) ($payload['enabled'] ?? false),
                'source_type' => $sourceType,
                'category_id' => $sourceType === BlogIndexSection::SOURCE_CATEGORY
                    ? ($payload['category_id'] ?? null)
                    : null,
                'title_override' => filled($payload['title_override'] ?? null)
                    ? trim((string) $payload['title_override'])
                    : null,
            ]);
        }

        return redirect()
            ->route('admin.blog-index-sections.index')
            ->with('success', 'Blog index sections updated.');
    }

    private function ensureSections()
    {
        foreach (BlogIndexSection::SECTION_KEYS as $sectionKey) {
            BlogIndexSection::query()->firstOrCreate(
                ['section_key' => $sectionKey],
                [
                    'enabled' => true,
                    'source_type' => BlogIndexSection::SOURCE_LATEST,
                ]
            );
        }

        $sections = BlogIndexSection::query()
            ->with('category:id,name,slug')
            ->whereIn('section_key', BlogIndexSection::SECTION_KEYS)
            ->get()
            ->sortBy(fn (BlogIndexSection $section) => array_search($section->section_key, BlogIndexSection::SECTION_KEYS, true))
            ->values();

        return $sections;
    }

    private function mapSection(?BlogIndexSection $section): array
    {
        return [
            'id' => $section?->id,
            'section_key' => $section?->section_key,
            'enabled' => (bool) ($section?->enabled ?? true),
            'source_type' => $section?->source_type ?? BlogIndexSection::SOURCE_LATEST,
            'category_id' => $section?->category_id,
            'title_override' => $section?->title_override,
        ];
    }
}
