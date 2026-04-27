<?php
/**
 * SEO Controller
 *
 * Serves /sitemap.xml and /robots.txt directly from PHP so we can keep them
 * in lock-step with the route table without checking in stale static files.
 * Static fallback files in the project root point to the same canonical
 * AviatorTutor URLs in case mod_rewrite is bypassed.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class SeoController extends Controller
{
    private const ORIGIN = 'https://aviatortutor.com';

    /** Public URLs included in the sitemap. */
    private const URLS = [
        ['/',          1.0, 'weekly'],
        ['/pricing',   0.9, 'monthly'],
        ['/about',     0.6, 'monthly'],
        ['/contact',   0.5, 'monthly'],
        ['/faq',       0.7, 'monthly'],
        ['/privacy',   0.3, 'yearly'],
        ['/terms',     0.3, 'yearly'],
        ['/login',     0.4, 'monthly'],
        ['/register',  0.7, 'monthly'],
        ['/coming-soon/cessna-caravan',    0.5, 'monthly'],
        ['/coming-soon/pilot-interview',   0.5, 'monthly'],
        ['/coming-soon/cabin-crew',        0.4, 'monthly'],
        ['/coming-soon/emergency',         0.4, 'monthly'],
        ['/coming-soon/crm',               0.4, 'monthly'],
        ['/coming-soon/general-aviation',  0.4, 'monthly'],
    ];

    public function sitemap(Request $request, Response $response): void
    {
        $today = date('Y-m-d');
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (self::URLS as [$path, $priority, $changefreq]) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . self::ORIGIN . $path . "</loc>\n";
            $xml .= '    <lastmod>' . $today . "</lastmod>\n";
            $xml .= '    <changefreq>' . $changefreq . "</changefreq>\n";
            $xml .= '    <priority>' . number_format($priority, 1) . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>' . "\n";

        $response->status(200);
        header('Content-Type: application/xml; charset=utf-8');
        echo $xml;
    }

    public function robots(Request $request, Response $response): void
    {
        $body = "User-agent: *\n"
              . "Allow: /\n"
              . "Disallow: /admin/\n"
              . "Disallow: /api/\n"
              . "Disallow: /redeem\n"
              . "Disallow: /account\n"
              . "Disallow: /dashboard\n"
              . "\n"
              . "Sitemap: " . self::ORIGIN . "/sitemap.xml\n";

        $response->status(200);
        header('Content-Type: text/plain; charset=utf-8');
        echo $body;
    }
}
