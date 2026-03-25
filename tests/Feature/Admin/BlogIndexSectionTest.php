<?php

namespace Tests\Feature\Admin;

use App\Models\BlogIndexSection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlogIndexSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_blog_index_sections_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.blog-index-sections.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/BlogIndexSections/Index')
                ->where('sections.wide_section.section_key', BlogIndexSection::KEY_WIDE)
                ->where('sections.cluster_left.section_key', BlogIndexSection::KEY_CLUSTER_LEFT)
                ->where('sections.cluster_right.section_key', BlogIndexSection::KEY_CLUSTER_RIGHT)
            );
    }

    public function test_admin_can_update_blog_index_sections(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $leftCategory = Category::factory()->create(['name' => 'Left Category', 'slug' => 'left-category']);
        $rightCategory = Category::factory()->create(['name' => 'Right Category', 'slug' => 'right-category']);

        $this->actingAs($admin)
            ->put(route('admin.blog-index-sections.update'), [
                'sections' => [
                    'wide_section' => [
                        'enabled' => true,
                        'source_type' => 'featured',
                        'category_id' => null,
                        'title_override' => 'Featured Picks',
                    ],
                    'cluster_left' => [
                        'enabled' => true,
                        'source_type' => 'category',
                        'category_id' => $leftCategory->id,
                        'title_override' => '',
                    ],
                    'cluster_right' => [
                        'enabled' => true,
                        'source_type' => 'category',
                        'category_id' => $rightCategory->id,
                        'title_override' => 'Right Cluster',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.blog-index-sections.index'));

        $this->assertDatabaseHas('blog_index_sections', [
            'section_key' => BlogIndexSection::KEY_WIDE,
            'source_type' => BlogIndexSection::SOURCE_FEATURED,
            'title_override' => 'Featured Picks',
        ]);

        $this->assertDatabaseHas('blog_index_sections', [
            'section_key' => BlogIndexSection::KEY_CLUSTER_LEFT,
            'source_type' => BlogIndexSection::SOURCE_CATEGORY,
            'category_id' => $leftCategory->id,
            'title_override' => null,
        ]);

        $this->assertDatabaseHas('blog_index_sections', [
            'section_key' => BlogIndexSection::KEY_CLUSTER_RIGHT,
            'source_type' => BlogIndexSection::SOURCE_CATEGORY,
            'category_id' => $rightCategory->id,
            'title_override' => 'Right Cluster',
        ]);
    }
}
