<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiPostImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_the_post_importer(): void
    {
        $this->get(route('admin.post-importer.index'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.post-importer.store'), [
            'package' => $this->validPackage(),
        ])->assertRedirect(route('login'));
    }

    public function test_non_admin_users_are_forbidden_from_the_post_importer(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.post-importer.index'))
            ->assertForbidden();
    }

    public function test_admin_can_import_a_post_package_into_a_draft_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.post-importer.store'), [
            'package' => $this->validPackage(),
        ]);

        $post = Post::query()->with(['category', 'tags'])->first();

        $this->assertNotNull($post);

        $response->assertRedirect(route('admin.posts.edit', $post));
        $response->assertSessionHas('success', 'Post imported as draft.');

        $this->assertSame('Buyer Mistakes: A Strategy Breakdown for Winning Smarter in Today\'s Market', $post->title);
        $this->assertSame(Post::STATUS_DRAFT, $post->status);
        $this->assertSame('Buyers', $post->category?->name);
        $this->assertCount(3, $post->tags);
        $this->assertStringContainsString('<p>Buying a home is exciting for a reason.</p>', $post->content);
    }

    private function validPackage(): string
    {
        return <<<'TEXT'
TITLE:
Buyer Mistakes: A Strategy Breakdown for Winning Smarter in Today's Market

ARTICLE:
Buying a home is exciting for a reason.

It feels like progress, possibility, and a real move forward.

LIST:
- SEO Title: Buyer Mistakes to Avoid: Smart Strategies for Today's Market
- Slug: buyer-mistakes-strategies
- Excerpt: Buyers have more room to negotiate in today's market, but that does not mean mistakes have disappeared.
- Sources: Freddie Mac PMMS; National Association of Realtors; Consumer Financial Protection Bureau
- Category: Buyers
- Tags: buyer mistakes, home buying strategy, first-time home buyers
- Meta Title: Buyer Mistakes to Avoid in 2026 | Smart Home Buying Strategies
- Meta Description: Learn the biggest buyer mistakes in today's housing market and how to avoid them.
- Canonical URL: https://www.example.com/blog/buyer-mistakes-strategies
- OG Title: Buyer Mistakes to Avoid: A Smart Strategy Breakdown
- OG Description: A clear, practical breakdown of common buyer mistakes.
- Featured Image Path: /images/blog/buyer-mistakes-strategies-cover.jpg
- OG Image Path: /images/blog/buyer-mistakes-strategies-og.jpg
- Noindex: No
TEXT;
    }
}
