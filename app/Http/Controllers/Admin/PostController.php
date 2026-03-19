<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');

        $allowedStatuses = ['all', Post::STATUS_DRAFT, Post::STATUS_PUBLISHED];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $posts = Post::query()
            ->with('category:id,name')
            ->select([
                'id',
                'title',
                'slug',
                'status',
                'published_at',
                'updated_at',
                'category_id',
                'featured_image_path',
            ])
            ->when($search !== '', fn ($q) => $q->where('title', 'like', '%' . $search . '%'))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'status' => $post->status,
                'category_name' => $post->category?->name,
                'published_at' => $post->published_at,
                'updated_at' => $post->updated_at,
                'featured_image_url' => $this->resolveMediaUrl($post->featured_image_path),
            ]);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Posts/Create', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);
        $validated['noindex'] = (bool) ($validated['noindex'] ?? false);

        $tagIds = Arr::pull($validated, 'tag_ids', []);
        $newTags = Arr::pull($validated, 'new_tags', []);
        $newCategory = Arr::pull($validated, 'new_category', null);

        if ($newCategory && trim($newCategory) !== '') {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($newCategory)],
                ['name' => $newCategory]
            );

            $validated['category_id'] = $category->id;
        }

        if (is_array($newTags)) {
            foreach ($newTags as $tagName) {
                if (!is_string($tagName) || trim($tagName) === '') {
                    continue;
                }

                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );

                $tagIds[] = $tag->id;
            }
        }

        $tagIds = array_values(array_unique(array_map('intval', $tagIds)));

        /** @var UploadedFile|null $featuredImage */
        $featuredImage = Arr::pull($validated, 'featured_image');
        $selectedFeaturedImagePath = Arr::pull($validated, 'featured_image_path');
        Arr::pull($validated, 'remove_featured_image');

        $baseSlug = ($validated['slug'] ?? '') !== ''
            ? (string) $validated['slug']
            : (string) $validated['title'];

        $validated['slug'] = $this->generateUniqueSlug($baseSlug);

        if ($featuredImage) {
            $validated['featured_image_path'] = $this->storeImageInBlogLibrary(
                $featuredImage,
                $validated['slug']
            );
        } elseif ($selectedFeaturedImagePath) {
            $validated['featured_image_path'] = $selectedFeaturedImagePath;
        }

        $userId = (int) $request->user()->id;

        $post = Post::create([
            ...$validated,
            'status' => Post::STATUS_DRAFT,
            'published_at' => null,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $post->tags()->sync($tagIds);

        return redirect()->route('admin.posts.edit', $post);
    }

    public function show(Post $post)
    {
        $post->load(['category:id,name', 'tags:id,name']);

        return Inertia::render('Admin/Posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'sources' => $post->sources,
                'category_name' => $post->category?->name,
                'tag_names' => $post->tags->pluck('name')->values()->all(),
                'featured_image_url' => $this->resolveMediaUrl($post->featured_image_path),
                'status' => $post->status,
                'published_at' => $post->published_at,
                'updated_at' => $post->updated_at,
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'canonical_url' => $post->canonical_url,
                'og_title' => $post->og_title,
                'og_description' => $post->og_description,
                'og_image_path' => $post->og_image_path,
                'noindex' => (bool) $post->noindex,
            ],
        ]);
    }

    public function edit(Post $post)
    {
        $featuredImageUrl = $this->resolveMediaUrl($post->featured_image_path);

        return Inertia::render('Admin/Posts/Edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'sources' => $post->sources,
                'category_id' => $post->category_id,
                'tag_ids' => $post->tags()->pluck('tags.id')->all(),

                'featured_image_path' => $post->featured_image_path,
                'featured_image_url' => $featuredImageUrl,

                'status' => $post->status,
                'published_at' => $post->published_at,

                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
                'canonical_url' => $post->canonical_url,
                'og_title' => $post->og_title,
                'og_description' => $post->og_description,
                'og_image_path' => $post->og_image_path,
                'noindex' => (bool) $post->noindex,
            ],
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validatePost($request);
        $validated['noindex'] = (bool) ($validated['noindex'] ?? false);

        $tagIds = Arr::pull($validated, 'tag_ids', []);
        $newTags = Arr::pull($validated, 'new_tags', []);
        $newCategory = Arr::pull($validated, 'new_category', null);

        if ($newCategory && trim($newCategory) !== '') {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($newCategory)],
                ['name' => $newCategory]
            );

            $validated['category_id'] = $category->id;
        }

        if (is_array($newTags)) {
            foreach ($newTags as $tagName) {
                if (!is_string($tagName) || trim($tagName) === '') {
                    continue;
                }

                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );

                $tagIds[] = $tag->id;
            }
        }

        $tagIds = array_values(array_unique(array_map('intval', $tagIds)));

        /** @var UploadedFile|null $featuredImage */
        $featuredImage = Arr::pull($validated, 'featured_image');
        $selectedFeaturedImagePath = Arr::pull($validated, 'featured_image_path');
        $removeFeaturedImage = (bool) Arr::pull($validated, 'remove_featured_image', false);

        $baseSlug = ($validated['slug'] ?? '') !== ''
            ? (string) $validated['slug']
            : (string) $validated['title'];

        $validated['slug'] = $this->generateUniqueSlug($baseSlug, $post->id);

        if ($featuredImage) {
            $validated['featured_image_path'] = $this->storeImageInBlogLibrary(
                $featuredImage,
                $validated['slug']
            );
        } elseif ($selectedFeaturedImagePath) {
            $validated['featured_image_path'] = $selectedFeaturedImagePath;
        } elseif ($removeFeaturedImage) {
            $validated['featured_image_path'] = null;
        }

        $post->fill([
            ...$validated,
            'updated_by' => (int) $request->user()->id,
        ])->save();

        $post->tags()->sync($tagIds);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Post saved.');
    }

    public function publish(Request $request, Post $post)
    {
        $post->forceFill([
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now(),
            'updated_by' => (int) $request->user()->id,
        ])->save();

        return redirect()->route('admin.posts.edit', $post);
    }

    public function unpublish(Request $request, Post $post)
    {
        $post->forceFill([
            'status' => Post::STATUS_DRAFT,
            'published_at' => null,
            'updated_by' => (int) $request->user()->id,
        ])->save();

        return redirect()->route('admin.posts.edit', $post);
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],

            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'sources' => ['nullable', 'string'],

            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'new_category' => ['nullable', 'string', 'max:255'],

            'new_tags' => ['nullable', 'array'],
            'new_tags.*' => ['string', 'max:255'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],

            'canonical_url' => ['nullable', 'string', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image_path' => ['nullable', 'string', 'max:2048'],

            'noindex' => ['sometimes', 'boolean'],

            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],

            'featured_image' => ['nullable', 'image', 'max:8192'],
            'featured_image_path' => [
                'nullable',
                'string',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if ($value && !Str::startsWith($value, ['/images/', '/storage/'])) {
                        $fail('The featured image path must be inside /images or /storage.');
                    }
                },
            ],
            'remove_featured_image' => ['sometimes', 'boolean'],
        ]);
    }

    private function generateUniqueSlug(string $input, ?int $ignoreId = null): string
    {
        $base = Str::slug($input);

        if ($base === '') {
            $base = 'post';
        }

        $existing = Post::query()
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->where(fn ($q) => $q->where('slug', $base)->orWhere('slug', 'like', $base . '-%'))
            ->pluck('slug')
            ->all();

        if (!in_array($base, $existing, true)) {
            return $base;
        }

        $i = 2;

        while (in_array($base . '-' . $i, $existing, true)) {
            $i++;
        }

        return $base . '-' . $i;
    }

    private function storeImageInBlogLibrary(UploadedFile $file, string $baseName): string
    {
        $dir = public_path('images/blog');

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $base = Str::slug($baseName);

        if ($base === '') {
            $base = 'image';
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');

        if ($extension === '') {
            $extension = 'jpg';
        }

        $filename = $base . '.' . $extension;
        $counter = 2;

        while (File::exists($dir . DIRECTORY_SEPARATOR . $filename)) {
            $filename = $base . '-' . $counter . '.' . $extension;
            $counter++;
        }

        $file->move($dir, $filename);

        return '/images/blog/' . $filename;
    }

        private function resolveMediaUrl(?string $path): ?string
{
    if (!$path) {
        return null;
    }

    $path = trim($path);

    if (Str::startsWith($path, ['http://', 'https://'])) {
        return $path;
    }

    if (Str::startsWith($path, '/storage/images/')) {
        return Str::replaceFirst('/storage/images/', '/images/', $path);
    }

    if (Str::startsWith($path, 'storage/images/')) {
        return '/' . Str::replaceFirst('storage/images/', 'images/', ltrim($path, '/'));
    }

    if (Str::startsWith($path, '/images/')) {
        return $path;
    }

    if (Str::startsWith($path, 'images/')) {
        return '/' . ltrim($path, '/');
    }

    if (Str::startsWith($path, '/storage/')) {
        return $path;
    }

    return Storage::disk('public')->url($path);
}
}
