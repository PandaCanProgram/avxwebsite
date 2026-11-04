<?php

namespace Tests\Feature;

use DOMDocument;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Support\Facades\Route;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SeoAndSecurityTest extends TestCase
{
    public function test_sitemap_lists_only_public_pages_on_the_configured_domain(): void
    {
        config(['app.url' => 'https://avx.example/']);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $document = new DOMDocument;
        $this->assertTrue($document->loadXML($response->getContent()));
        $this->assertSame('http://www.sitemaps.org/schemas/sitemap/0.9', $document->documentElement->namespaceURI);
        $urls = [];

        foreach ($document->getElementsByTagName('loc') as $location) {
            $urls[] = $location->textContent;
        }

        $this->assertSame([
            'https://avx.example/',
            'https://avx.example/projects',
            'https://avx.example/investment',
            'https://avx.example/drone-platforms',
        ], $urls);
    }

    public function test_robots_advertises_the_configured_sitemap_instead_of_the_request_host(): void
    {
        config(['app.url' => 'https://avx.example/']);

        $response = $this->get('https://untrusted.example/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertContent("User-agent: *\nAllow: /\n\nSitemap: https://avx.example/sitemap.xml\n");
    }

    #[TestWith(['/'])]
    #[TestWith(['/projects'])]
    #[TestWith(['/investment'])]
    #[TestWith(['/drone-platforms'])]
    #[TestWith(['/sitemap.xml'])]
    #[TestWith(['/robots.txt'])]
    public function test_public_responses_prevent_cross_origin_framing(string $path): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'none';");
        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_not_found_responses_keep_frame_protection(): void
    {
        $response = $this->get('/missing-page-for-security-check');

        $response->assertNotFound();
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'none';");
        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_frame_protection_preserves_an_existing_content_security_policy(): void
    {
        Route::get('/existing-policy-check', fn () => response('Existing policy')
            ->header('Content-Security-Policy', "default-src 'self';"));

        $response = $this->get('/existing-policy-check');

        $response->assertOk();
        $this->assertSame([
            "default-src 'self';",
            "frame-ancestors 'none';",
        ], $response->headers->all('Content-Security-Policy'));
    }

    public function test_maintenance_responses_keep_frame_protection(): void
    {
        $this->mock(MaintenanceMode::class, function (MockInterface $mock): void {
            $mock->shouldReceive('active')->andReturn(true);
            $mock->shouldReceive('data')->andReturn(['status' => 503]);
        });

        $response = $this->get('/');

        $response->assertServiceUnavailable();
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'none';");
        $response->assertHeader('X-Frame-Options', 'DENY');
    }
}
