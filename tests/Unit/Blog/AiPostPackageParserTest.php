<?php

namespace Tests\Unit\Blog;

use App\Services\Blog\AiPostPackageParser;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AiPostPackageParserTest extends TestCase
{
    public function test_it_parses_a_valid_package(): void
    {
        $parser = new AiPostPackageParser();

        $parsed = $parser->parse($this->validPackage());

        $this->assertSame('Buyer Mistakes: A Strategy Breakdown for Winning Smarter in Today\'s Market', $parsed['title']);
        $this->assertSame('buyer-mistakes-strategies', $parsed['slug']);
        $this->assertSame('Buyers', $parsed['category']);
        $this->assertSame(['buyer mistakes', 'home buying strategy', 'first-time home buyers'], $parsed['tags']);
        $this->assertFalse($parsed['noindex']);
    }

    public function test_it_rejects_wrong_list_order(): void
    {
        $this->expectException(ValidationException::class);

        $parser = new AiPostPackageParser();
        $parser->parse(str_replace('- SEO Title:', '- Slug:', $this->validPackage()));
    }

    private function validPackage(): string
    {
        return <<<'TEXT'
TITLE:
Buyer Mistakes: A Strategy Breakdown for Winning Smarter in Today's Market

ARTICLE:
<p>Buying a home is exciting for a reason.</p>
<p>It feels like progress, possibility, and a real move forward.</p>

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
