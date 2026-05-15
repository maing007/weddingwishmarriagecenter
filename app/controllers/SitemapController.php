<?php

require_once __DIR__ . '/../models/BlogModel.php';

class SitemapController
{
    public function index(): void
    {
        $base = rtrim(BASE_URL, '/');
        $urls = [];

        $static = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/privacy', 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => '/demograph', 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => '/child', 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => '/faq', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/register', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => '/carees', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/sucess1', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/sucess2', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/sucess3', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/blogs', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/member', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/search', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/login', 'priority' => '0.5', 'changefreq' => 'yearly'],
        ];

        foreach ($static as $row) {
            $urls[] = [
                'loc'        => $base . $row['loc'],
                'lastmod'    => gmdate('Y-m-d'),
                'changefreq' => $row['changefreq'],
                'priority'   => $row['priority'],
            ];
        }

        try {
            $blogModel = new BlogModel();
            foreach ($blogModel->getPublishedSlugsForSitemap() as $row) {
                $slug = trim((string) ($row['slug'] ?? ''));
                if ($slug === '') {
                    continue;
                }
                $last = $row['created_at'] ?? null;
                $lastmod = is_string($last) && $last !== '' ? gmdate('Y-m-d', strtotime($last)) : gmdate('Y-m-d');
                $urls[] = [
                    'loc'        => $base . '/blog/' . rawurlencode($slug),
                    'lastmod'    => $lastmod,
                    'changefreq' => 'monthly',
                    'priority'   => '0.7',
                ];
            }
        } catch (Throwable $e) {
            // omit blog URLs if DB unavailable
        }

        header('Content-Type: application/xml; charset=UTF-8');
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo '  <url>' . "\n";
            echo '    <loc>' . $this->xmlEscape($u['loc']) . '</loc>' . "\n";
            echo '    <lastmod>' . $this->xmlEscape($u['lastmod']) . '</lastmod>' . "\n";
            echo '    <changefreq>' . $this->xmlEscape($u['changefreq']) . '</changefreq>' . "\n";
            echo '    <priority>' . $this->xmlEscape($u['priority']) . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }
        echo '</urlset>';
    }

    private function xmlEscape(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
