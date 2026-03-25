<?php

namespace App\Services\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AiPostImportService
{
    public function __construct(
        private readonly AiPostPackageParser $parser,
    ) {
    }

    public function import(string $package, User $user): Post
    {
        $parsed = $this->parser->parse($package);
        $validated = $this->validateParsed($parsed);

        return DB::transaction(function () use ($validated, $user) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($validated['category'])],
                ['name' => $validated['category']]
            );

            $tagIds = collect($validated['tags'])
                ->map(function (string $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );

                    return $tag->id;
                })
                ->unique()
                ->values()
                ->all();

            $post = Post::create([
                'title' => $validated['title'],
                'slug' => $this->generateUniqueSlug($validated['slug']),
                'excerpt' => $validated['excerpt'],
                'content' => $this->normalizeArticleContent($validated['article']),
                'sources' => $validated['sources'],
                'featured_image_path' => $validated['featured_image_path'] ?: null,
                'status' => Post::STATUS_DRAFT,
                'published_at' => null,
                'meta_title' => $validated['meta_title'],
                'meta_description' => $validated['meta_description'],
                'canonical_url' => $validated['canonical_url'],
                'og_title' => $validated['og_title'],
                'og_description' => $validated['og_description'],
                'og_image_path' => $validated['og_image_path'] ?: null,
                'noindex' => $validated['noindex'],
                'category_id' => $category->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $post->tags()->sync($tagIds);

            return $post;
        });
    }

    /**
     * @param array<string, mixed> $parsed
     * @return array<string, mixed>
     */
    private function validateParsed(array $parsed): array
    {
        $validator = Validator::make($parsed, [
            'title' => ['required', 'string', 'max:255'],
            'article' => ['required', 'string', 'min:40'],
            'seo_title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('posts', 'slug')],
            'excerpt' => ['required', 'string', 'max:1000'],
            'sources' => ['required', 'string', 'max:1000'],
            'category' => ['required', 'string', 'max:120'],
            'tags' => ['required', 'array', 'min:1', 'max:12'],
            'tags.*' => ['required', 'string', 'max:80'],
            'meta_title' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:320'],
            'canonical_url' => ['required', 'string', 'max:2048', 'url'],
            'og_title' => ['required', 'string', 'max:255'],
            'og_description' => ['required', 'string', 'max:320'],
            'featured_image_path' => ['nullable', 'string', 'max:2048'],
            'og_image_path' => ['nullable', 'string', 'max:2048'],
            'noindex' => ['required', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($parsed) {
            foreach (['featured_image_path', 'og_image_path'] as $field) {
                $value = trim((string) ($parsed[$field] ?? ''));

                if ($value !== '' && ! $this->isAllowedImagePath($value)) {
                    $validator->errors()->add($field, ucfirst(str_replace('_', ' ', $field)) . ' must be a safe internal /images/... path.');
                }
            }
        });

        return $validator->validate();
    }

    private function isAllowedImagePath(string $path): bool
    {
        if (str_contains($path, '..')) {
            return false;
        }

        return (bool) preg_match('#^/images/[A-Za-z0-9/_\-.]+$#', $path);
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = Str::slug($baseSlug);

        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => 'The slug field could not be normalized into a valid slug.',
            ]);
        }

        $candidate = $slug;
        $counter = 2;

        while (Post::where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function normalizeArticleContent(string $article): string
    {
        $article = trim($article);

        if ($article === '') {
            throw ValidationException::withMessages([
                'article' => 'The ARTICLE section cannot be empty.',
            ]);
        }

        if (! preg_match('/<\s*[a-z][^>]*>/i', $article)) {
            $paragraphs = preg_split('/\n{2,}/', $article) ?: [];

            $htmlParagraphs = array_map(function (string $paragraph): string {
                $paragraph = trim($paragraph);
                $paragraph = htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $paragraph = nl2br($paragraph, false);

                return '<p>' . $paragraph . '</p>';
            }, array_filter($paragraphs, static fn (string $paragraph): bool => trim($paragraph) !== ''));

            $article = implode("\n", $htmlParagraphs);
        }

        return $this->sanitizeHtml($article);
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? $html;
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html) ?? $html;
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/href\s*=\s*"\s*(javascript:|data:)[^"]*"/i', 'href="#"', $html) ?? $html;
        $html = preg_replace('/href\s*=\s*\'\s*(javascript:|data:)[^\']*\'/i', 'href="#"', $html) ?? $html;

        $allowed = '<p><br><strong><b><em><i><ul><ol><li><h2><h3><h4><blockquote><a>';
        $html = strip_tags($html, $allowed);

        return trim($html);
    }
}
