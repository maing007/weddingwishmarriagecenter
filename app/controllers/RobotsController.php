<?php

class RobotsController
{
    public function index(): void
    {
        $base = rtrim(BASE_URL, '/');
        header('Content-Type: text/plain; charset=UTF-8');

        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /dashboard/\n";
        echo "Disallow: /mail/\n";
        echo "Disallow: /adminusers/\n";
        echo "\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
    }
}
