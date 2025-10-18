<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Research;
use Illuminate\Support\Facades\URL;

class SEOService
{
    /**
     * Generate meta tags for pages and research.
     */
    public function generateMetaTags(Page|Research $model): array
    {
        $metaTags = [];

        if ($model instanceof Page) {
            $metaTags['title'] = $model->meta_title ?: $model->title;
            $metaTags['description'] = $model->meta_description ?: $model->excerpt;
            $metaTags['url'] = URL::to('/pages/' . $model->slug);
            $metaTags['image'] = $model->og_image ? URL::to($model->og_image) : null;
            $metaTags['type'] = 'website';
        } elseif ($model instanceof Research) {
            $metaTags['title'] = $model->title;
            $metaTags['description'] = $model->abstract ? substr($model->abstract, 0, 160) : '';
            $metaTags['url'] = URL::to('/research/' . $model->slug);
            $metaTags['image'] = $model->thumbnail ? URL::to($model->thumbnail) : null;
            $metaTags['type'] = 'article';
            $metaTags['published_time'] = $model->publication_date?->toIso8601String();
            $metaTags['authors'] = $model->authors;
        }

        return $metaTags;
    }

    /**
     * Generate structured data (Schema.org) markup.
     */
    public function generateStructuredData(Page|Research $model): array
    {
        if ($model instanceof Page) {
            return $this->generatePageStructuredData($model);
        } elseif ($model instanceof Research) {
            return $this->generateResearchStructuredData($model);
        }

        return [];
    }

    /**
     * Generate structured data for pages.
     */
    private function generatePageStructuredData(Page $page): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page->title,
            'description' => $page->excerpt,
            'url' => URL::to('/pages/' . $page->slug),
            'datePublished' => $page->published_at?->toIso8601String(),
            'dateModified' => $page->updated_at->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'HEAC'),
                'url' => URL::to('/'),
            ],
        ];
    }

    /**
     * Generate structured data for research.
     */
    private function generateResearchStructuredData(Research $research): array
    {
        $authors = [];
        if (is_array($research->authors)) {
            foreach ($research->authors as $author) {
                $authors[] = [
                    '@type' => 'Person',
                    'name' => is_string($author) ? $author : ($author['name'] ?? 'Unknown'),
                ];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => $research->title,
            'abstract' => $research->abstract,
            'url' => URL::to('/research/' . $research->slug),
            'datePublished' => $research->publication_date?->toIso8601String(),
            'dateModified' => $research->updated_at->toIso8601String(),
            'author' => $authors,
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'HEAC'),
                'url' => URL::to('/'),
            ],
            'keywords' => $research->tags->pluck('name')->implode(', '),
        ];
    }

    /**
     * Generate sitemap XML.
     */
    public function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add homepage
        $xml .= $this->generateSitemapUrl(URL::to('/'), now(), 'daily', '1.0');

        // Add published pages
        $pages = Page::published()->get();
        foreach ($pages as $page) {
            $xml .= $this->generateSitemapUrl(
                URL::to('/pages/' . $page->slug),
                $page->updated_at,
                'weekly',
                '0.8'
            );
        }

        // Add published research
        $research = Research::published()->get();
        foreach ($research as $item) {
            $xml .= $this->generateSitemapUrl(
                URL::to('/research/' . $item->slug),
                $item->updated_at,
                'monthly',
                '0.7'
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Generate a single sitemap URL entry.
     */
    private function generateSitemapUrl(
        string $loc,
        $lastmod,
        string $changefreq = 'weekly',
        string $priority = '0.5'
    ): string {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        $xml .= '<lastmod>' . $lastmod->toW3cString() . '</lastmod>';
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }

    /**
     * Generate Open Graph tags for social sharing.
     */
    public function generateOpenGraphTags(Page|Research $model): array
    {
        $metaTags = $this->generateMetaTags($model);

        $ogTags = [
            'og:title' => $metaTags['title'],
            'og:description' => $metaTags['description'],
            'og:url' => $metaTags['url'],
            'og:type' => $metaTags['type'],
            'og:site_name' => config('app.name', 'HEAC'),
        ];

        if (!empty($metaTags['image'])) {
            $ogTags['og:image'] = $metaTags['image'];
        }

        if ($model instanceof Research && !empty($metaTags['published_time'])) {
            $ogTags['article:published_time'] = $metaTags['published_time'];
            
            if (!empty($metaTags['authors'])) {
                foreach ($metaTags['authors'] as $author) {
                    $authorName = is_string($author) ? $author : ($author['name'] ?? '');
                    if ($authorName) {
                        $ogTags['article:author'][] = $authorName;
                    }
                }
            }

            // Add tags
            if ($model->tags) {
                foreach ($model->tags as $tag) {
                    $ogTags['article:tag'][] = $tag->name;
                }
            }
        }

        // Twitter Card tags
        $ogTags['twitter:card'] = 'summary_large_image';
        $ogTags['twitter:title'] = $metaTags['title'];
        $ogTags['twitter:description'] = $metaTags['description'];
        if (!empty($metaTags['image'])) {
            $ogTags['twitter:image'] = $metaTags['image'];
        }

        return $ogTags;
    }

    /**
     * Generate organization structured data for HEAC.
     */
    public function generateOrganizationStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name', 'HEAC'),
            'url' => URL::to('/'),
            'logo' => URL::to('/images/logo.png'),
            'description' => 'Higher Education Accreditation Commission',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'Customer Service',
                'url' => URL::to('/contact'),
            ],
        ];
    }
}
