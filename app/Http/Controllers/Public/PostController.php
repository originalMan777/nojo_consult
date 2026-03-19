<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->published()
            ->with(['category:id,name,slug'])
            ->orderByDesc('published_at')
            ->select(['id', 'title', 'slug', 'excerpt', 'published_at', 'category_id', 'featured_image_path'])
            ->paginate(10)
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'published_at' => $post->published_at,
                'featured_image_url' => $post->featured_image_url,
                'category' => $post->category
                    ? [
                        'name' => $post->category->name,
                        'slug' => $post->category->slug,
                    ]
                    : null,
            ]);

        $categories = Category::query()
            ->withCount([
                'posts' => fn ($query) => $query->published(),
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => $category->posts_count,
            ])
            ->values();

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'category:id,name,slug',
                'tags:id,name,slug',
            ])
            ->firstOrFail();

        $postUrl = route('blog.show', ['slug' => $post->slug]);

        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post->content)));

        $title = $post->meta_title ?: $post->title;

        $description = $post->meta_description
            ?: ($post->excerpt ?: Str::limit($plainContent, 160, ''));

        $ogTitle = $post->og_title ?: ($post->meta_title ?: $post->title);

        $ogDescription = $post->og_description
            ?: ($post->meta_description ?: ($post->excerpt ?: Str::limit($plainContent, 160, '')));

        $ogImage = $post->og_image_path ?: ($post->featured_image_url ?: null);

        $canonical = $post->canonical_url ?: $postUrl;

        $robots = $post->noindex ? 'noindex,nofollow' : 'index,follow';

        return Inertia::render('Blog/Show', [
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'published_at' => $post->published_at,
                'featured_image_url' => $post->featured_image_url,

                // Quill already stores HTML, so send it straight through
                'content_html' => $post->content,

                'sources' => $post->sources,

                'category' => $post->category
                    ? [
                        'name' => $post->category->name,
                        'slug' => $post->category->slug,
                    ]
                    : null,

                'tags' => $post->tags->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ])->values(),

                'seo' => [
                    'url' => $postUrl,
                    'canonical_url' => $canonical,
                    'robots' => $robots,

                    'title' => $title,
                    'description' => $description,

                    'og' => [
                        'type' => 'article',
                        'title' => $ogTitle,
                        'description' => $ogDescription,
                        'image' => $ogImage,
                        'url' => $postUrl,
                    ],

                    'twitter' => [
                        'card' => 'summary_large_image',
                        'title' => $ogTitle,
                        'description' => $ogDescription,
                        'image' => $ogImage,
                    ],
                ],
            ],
        ]);
    }

    public function category(Request $request, string $slug)
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->firstOrFail(['id', 'name', 'slug']);

        $posts = Post::query()
            ->published()
            ->where('category_id', $category->id)
            ->orderByDesc('published_at')
            ->select(['id', 'title', 'slug', 'excerpt', 'published_at', 'featured_image_path'])
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'published_at' => $post->published_at,
                'featured_image_url' => $post->featured_image_url,
            ]);

        return Inertia::render('Blog/Category', [
            'seo' => [
                'title' => 'Category: ' . $category->name,
                'description' => 'Articles filed under ' . $category->name . '.',
                'canonical_url' => route('blog.category', ['slug' => $category->slug]),
            ],
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'posts' => $posts,
        ]);
    }

    public function tag(Request $request, string $slug)
    {
        $tag = Tag::query()
            ->where('slug', $slug)
            ->firstOrFail(['id', 'name', 'slug']);

        $posts = Post::query()
            ->published()
            ->whereHas('tags', fn ($q) => $q->whereKey($tag->id))
            ->orderByDesc('published_at')
            ->select(['id', 'title', 'slug', 'excerpt', 'published_at', 'featured_image_path'])
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'published_at' => $post->published_at,
                'featured_image_url' => $post->featured_image_url,
            ]);

        return Inertia::render('Blog/Tag', [
            'seo' => [
                'title' => 'Tag: ' . $tag->name,
                'description' => 'Articles tagged with ' . $tag->name . '.',
                'canonical_url' => route('blog.tag', ['slug' => $tag->slug]),
            ],
            'tag' => [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ],
            'posts' => $posts,
        ]);
    }
}
