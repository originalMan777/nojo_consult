<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(4, true));
        $user = User::factory();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(),
            'content' => '<p>' . fake()->paragraph() . '</p>',
            'sources' => fake()->url(),
            'featured_image_path' => null,
            'status' => Post::STATUS_DRAFT,
            'published_at' => null,
            'meta_title' => null,
            'meta_description' => null,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image_path' => null,
            'noindex' => false,
            'category_id' => Category::factory(),
            'created_by' => $user,
            'updated_by' => $user,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now()->subMinute(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now()->addDay(),
        ]);
    }
}
