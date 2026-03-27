<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

class RecipeLoremSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()
            ->where('is_admin', true)
            ->first()
            ?? User::query()->first();

        if (! $user) {
            throw new RuntimeException('RecipeLoremSeeder requires at least one existing user.');
        }

        $category = Category::query()->firstOrCreate(
            ['slug' => 'recipes'],
            ['name' => 'Recipes']
        );

        $tagNames = [
            'recipes',
            'placeholder content',
            'lorem ipsum',
            'cooking',
            'meal ideas',
            'kitchen',
            'food templates',
            'draft content',
        ];

        $tagIds = collect($tagNames)
            ->map(function (string $name) {
                $tag = Tag::query()->firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );

                return $tag->id;
            })
            ->all();

        $titles = [
            'Sunday Pasta Bake Template',
            'Simple Herb Chicken Placeholder',
            'Weeknight Soup Recipe Draft',
            'Roasted Vegetable Bowl Template',
            'Quick Breakfast Skillet Draft',
            'Lemon Garlic Fish Placeholder',
            'Easy Family Chili Template',
            'Creamy Mushroom Pasta Draft',
            'Sheet Pan Dinner Placeholder',
            'Slow Cooker Stew Template',
            'Fresh Garden Salad Draft',
            'Baked Rice Casserole Placeholder',
            'Homestyle Burger Recipe Draft',
            'Tomato Basil Chicken Template',
            'Classic Taco Night Placeholder',
            'Garlic Butter Shrimp Draft',
            'Hearty Bean Soup Template',
            'Spiced Roasted Potatoes Draft',
            'Cheesy Chicken Bake Placeholder',
            'Simple Stir Fry Template',
            'Cozy Pot Pie Recipe Draft',
            'Honey Glazed Salmon Placeholder',
            'Rustic Bread Bowl Soup Draft',
            'Easy Oven Chicken Template',
            'Savory Breakfast Hash Placeholder',
            'Family Lasagna Draft',
            'Herb Rice and Vegetables Template',
            'Skillet Sausage Dinner Placeholder',
            'Warm Lentil Bowl Draft',
            'Creamy Potato Soup Template',
            'Baked Meatball Dish Placeholder',
            'Weekend Brunch Casserole Draft',
        ];

        $baseExcerpt = 'Lorem ipsum placeholder recipe content for layout, workflow, and archive testing. This draft is designed to populate the recipe section without using final editorial copy.';

        foreach ($titles as $index => $title) {
            $slug = Str::slug($title);

            $body = $this->buildContent($title, $index + 1);

            $post = Post::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'excerpt' => $baseExcerpt,
                    'content' => $body,
                    'sources' => null,
                    'featured_image_path' => null,
                    'is_featured' => false,
                    'status' => Post::STATUS_DRAFT,
                    'published_at' => null,
                    'meta_title' => null,
                    'meta_description' => null,
                    'canonical_url' => null,
                    'og_title' => null,
                    'og_description' => null,
                    'og_image_path' => null,
                    'noindex' => false,
                    'category_id' => $category->id,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'updated_at' => Carbon::now()->subMinutes(max(0, 32 - $index)),
                ]
            );

            $post->tags()->sync($tagIds);
        }
    }

    private function buildContent(string $title, int $number): string
    {
        $paragraphOne = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi.';
        $paragraphTwo = 'Proin porttitor, orci nec nonummy molestie, enim est eleifend mi, non fermentum diam nisl sit amet erat. Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim. Pellentesque congue. Ut in risus volutpat libero pharetra tempor.';
        $paragraphThree = 'Cras vestibulum bibendum augue. Praesent egestas leo in pede. Praesent blandit odio eu enim. Pellentesque sed dui ut augue blandit sodales. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aliquam nibh.';

        return <<<HTML
<h2>{$title}</h2>
<p>{$paragraphOne}</p>
<p>{$paragraphTwo}</p>
<h3>Recipe Template Notes {$number}</h3>
<p>{$paragraphThree}</p>
<ul>
    <li>Lorem ipsum placeholder ingredient line one.</li>
    <li>Lorem ipsum placeholder ingredient line two.</li>
    <li>Lorem ipsum placeholder ingredient line three.</li>
</ul>
<p>{$paragraphOne}</p>
HTML;
    }
}
