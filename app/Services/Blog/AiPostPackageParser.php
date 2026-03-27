<?php

namespace App\Services\Blog;

use Illuminate\Validation\ValidationException;

class AiPostPackageParser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $package): array
    {
        $normalized = str_replace(["
", ""], "
", trim($package));
        $normalized = preg_replace('/^ï»¿/', '', $normalized) ?? $normalized;

        if ($normalized === '') {
            throw ValidationException::withMessages([
                'package' => 'Paste a complete post package before importing.',
            ]);
        }

        if (! preg_match('/\ATITLE:\s*
?(.*?)
+ARTICLE:\s*
?(.*?)
+LIST:\s*
?(.*)\z/s', $normalized, $matches)) {
            throw ValidationException::withMessages([
                'package' => 'The package must contain TITLE:, ARTICLE:, and LIST: sections in the exact order.',
            ]);
        }

        $title = trim($matches[1]);
        $article = trim($matches[2]);
        $list = trim($matches[3]);

        if ($title === '' || $article === '' || $list === '') {
            throw ValidationException::withMessages([
                'package' => 'TITLE, ARTICLE, and LIST must all contain content.',
            ]);
        }

        $expectedLabels = [
            'SEO Title',
            'Slug',
            'Excerpt',
            'Sources',
            'Category',
            'Tags',
            'Meta Title',
            'Meta Description',
            'Canonical URL',
            'OG Title',
            'OG Description',
            'Featured Image Path',
            'OG Image Path',
            'Noindex',
        ];

        $lines = preg_split('/
+/', $list) ?: [];
        $parsedLabels = [];
        $parsedValues = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (! preg_match('/^(?:[-*•]\s+)?([^:]+):\s*(.*)\z/u', $line, $lineMatches)) {
                throw ValidationException::withMessages([
                    'package' => 'Each LIST line must use Label: value format, with or without a copied bullet prefix.',
                ]);
            }

            $label = trim($lineMatches[1]);
            $value = trim($lineMatches[2]);

            $parsedLabels[] = $label;
            $parsedValues[$label] = $value;
        }

        if ($parsedLabels !== $expectedLabels) {
            throw ValidationException::withMessages([
                'package' => 'The LIST fields must use the exact labels and order required by the Post Importer.',
            ]);
        }

        $noindex = $parsedValues['Noindex'];

        if (! in_array($noindex, ['Yes', 'No'], true)) {
            throw ValidationException::withMessages([
                'package' => 'Noindex must be either Yes or No.',
            ]);
        }

        $tags = array_values(array_filter(array_map(
            static fn (string $tag): string => trim($tag),
            explode(',', $parsedValues['Tags'])
        )));

        if ($tags === []) {
            throw ValidationException::withMessages([
                'package' => 'Tags must contain at least one comma-separated tag.',
            ]);
        }

        return [
            'title' => $title,
            'article' => $article,
            'seo_title' => $parsedValues['SEO Title'],
            'slug' => $parsedValues['Slug'],
            'excerpt' => $parsedValues['Excerpt'],
            'sources' => $parsedValues['Sources'],
            'category' => $parsedValues['Category'],
            'tags' => $tags,
            'meta_title' => $parsedValues['Meta Title'],
            'meta_description' => $parsedValues['Meta Description'],
            'canonical_url' => $parsedValues['Canonical URL'],
            'og_title' => $parsedValues['OG Title'],
            'og_description' => $parsedValues['OG Description'],
            'featured_image_path' => $parsedValues['Featured Image Path'],
            'og_image_path' => $parsedValues['OG Image Path'],
            'noindex' => $noindex === 'Yes',
        ];
    }
}
