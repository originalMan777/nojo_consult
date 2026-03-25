<?php

namespace Tests\Feature\Blog;

use App\Models\BlogIndexSection;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlogIndexAssemblyTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_includes_wide_and_cluster_section_payloads(): void
    {
        $leftCategory = Category::factory()->create(['name' => 'Marketing', 'slug' => 'marketing']);
        $rightCategory = Category::factory()->create(['name' => 'Buyers', 'slug' => 'buyers']);
        $mainCategory = Category::factory()->create(['name' => 'General', 'slug' => 'general']);

        Post::factory()->published()->count(18)->create([
            'category_id' => $mainCategory->id,
        ]);

        Post::factory()->published()->count(6)->create([
            'category_id' => $mainCategory->id,
            'is_featured' => true,
        ]);

        Post::factory()->published()->count(4)->create([
            'category_id' => $leftCategory->id,
        ]);

        Post::factory()->published()->count(4)->create([
            'category_id' => $rightCategory->id,
        ]);

        BlogIndexSection::query()->where('section_key', BlogIndexSection::KEY_WIDE)->update([
            'enabled' => true,
            'source_type' => BlogIndexSection::SOURCE_FEATURED,
            'title_override' => 'Featured Picks',
        ]);

        BlogIndexSection::query()->where('section_key', BlogIndexSection::KEY_CLUSTER_LEFT)->update([
            'enabled' => true,
            'source_type' => BlogIndexSection::SOURCE_CATEGORY,
            'category_id' => $leftCategory->id,
            'title_override' => null,
        ]);

        BlogIndexSection::query()->where('section_key', BlogIndexSection::KEY_CLUSTER_RIGHT)->update([
            'enabled' => true,
            'source_type' => BlogIndexSection::SOURCE_CATEGORY,
            'category_id' => $rightCategory->id,
            'title_override' => 'From Buyers',
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Index')
                ->has('posts.data', 18)
                ->where('wideSection.title', 'Featured Picks')
                ->has('wideSection.posts', 6)
                ->where('clusterSection.left.title', 'From Marketing')
                ->has('clusterSection.left.posts', 4)
                ->where('clusterSection.right.title', 'From Buyers')
                ->has('clusterSection.right.posts', 4)
            );
    }
}
