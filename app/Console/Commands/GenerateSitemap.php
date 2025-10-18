<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Research;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for the website';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        // Add homepage
        $this->addUrl($xml, url('/'), now(), 'daily', '1.0');

        // Add published pages
        $pages = Page::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($pages as $page) {
            $this->addUrl(
                $xml,
                route('pages.show', $page->slug),
                $page->updated_at,
                'weekly',
                '0.8'
            );
        }

        // Add research listing page
        $this->addUrl($xml, route('research.index'), now(), 'daily', '0.9');

        // Add published research
        $research = Research::where('status', 'published')
            ->get();

        foreach ($research as $item) {
            $this->addUrl(
                $xml,
                route('research.show', $item->slug),
                $item->updated_at,
                'monthly',
                '0.7'
            );
        }

        // Add contact page
        $this->addUrl($xml, route('contact.index'), now(), 'monthly', '0.6');

        // Save sitemap to public directory
        $sitemapPath = public_path('sitemap.xml');
        file_put_contents($sitemapPath, $xml->asXML());

        $this->info('Sitemap generated successfully at: ' . $sitemapPath);
        $this->info('Total URLs: ' . count($xml->url));

        return Command::SUCCESS;
    }

    private function addUrl(\SimpleXMLElement $xml, string $loc, $lastmod, string $changefreq, string $priority): void
    {
        $url = $xml->addChild('url');
        $url->addChild('loc', htmlspecialchars($loc));
        $url->addChild('lastmod', $lastmod->toAtomString());
        $url->addChild('changefreq', $changefreq);
        $url->addChild('priority', $priority);
    }
}
