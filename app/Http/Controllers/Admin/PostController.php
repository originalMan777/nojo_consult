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
            'navigator' => $this->buildPostNavigator($post),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);
        $validated['noindex'] = (bool) ($validated['noindex'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

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
            'navigator' => $this->buildPostNavigator($post),
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
                'is_featured' => (bool) $post->is_featured,
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
            'navigator' => $this->buildPostNavigator($post),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validatePost($request);
        $validated['noindex'] = (bool) ($validated['noindex'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

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

    public function publish(Post $post)
{
    $post->update([
        'status' => Post::STATUS_PUBLISHED,
        'published_at' => now(),
        'updated_by' => auth()->id(),
    ]);

    return back()->with('success', 'Post published successfully.');
}

public function unpublish(Post $post)
{
    $post->update([
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
        'updated_by' => auth()->id(),
    ]);

    return back()->with('success', 'Post unpublished successfully.');
}

public function destroy(Post $post)
{
    $post->tags()->detach();
    $post->delete();

    return back()->with('success', 'Post deleted successfully.');
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
            'is_featured' => ['sometimes', 'boolean'],
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
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->where('slug', 'like', $base . '%')
            ->pluck('slug');

        if (!$existing->contains($base)) {
            return $base;
        }

        $counter = 2;

        do {
            $candidate = $base . '-' . $counter;
            $counter++;
        } while ($existing->contains($candidate));

        return $candidate;
    }

    private function buildPostNavigator(Post $post): array
    {
        $orderedIds = Post::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->pluck('id')
            ->values();

        $currentIndex = $orderedIds->search($post->id);

        if ($currentIndex === false) {
            return [
                'previous' => null,
                'next' => null,
            ];
        }

        $previousId = $currentIndex > 0 ? $orderedIds->get($currentIndex - 1) : null;
        $nextId = $currentIndex < ($orderedIds->count() - 1) ? $orderedIds->get($currentIndex + 1) : null;

        $neighbors = Post::query()
            ->whereIn('id', array_filter([$previousId, $nextId]))
            ->get(['id', 'title'])
            ->keyBy('id');

        $mapPost = static fn (?int $id) => $id && $neighbors->has($id)
            ? [
                'id' => $id,
                'title' => $neighbors->get($id)?->title,
            ]
            : null;

        return [
            'previous' => $mapPost($previousId),
            'next' => $mapPost($nextId),
        ];
    }

    private function storeImageInBlogLibrary(UploadedFile $file, string $baseName): string
    {
        $directory = public_path('images/blog');

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::slug($baseName) ?: 'post-image';
        $candidate = $filename . '.' . $extension;
        $counter = 2;

        while (File::exists($directory . DIRECTORY_SEPARATOR . $candidate)) {
            $candidate = $filename . '-' . $counter . '.' . $extension;
            $counter++;
        }

        $file->move($directory, $candidate);

        return '/images/blog/' . $candidate;
    }

    private function resolveMediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = trim($path);

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
