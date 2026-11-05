<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    #[TestWith(['/'])]
    #[TestWith(['/projects'])]
    #[TestWith(['/investment'])]
    #[TestWith(['/drone-platforms'])]
    public function test_production_pages_include_the_configured_google_tag_once(string $path): void
    {
        $this->app->instance('env', 'production');
        config(['services.google_analytics.enabled' => true, 'services.google_analytics.measurement_id' => 'G-TEST123']);

        $response = $this->get($path);

        $response->assertOk();
        $response->assertSee("gtag('config', 'G-TEST123')", false);
        $this->assertSame(1, substr_count($response->getContent(), 'https://www.googletagmanager.com/gtag/js?id=G-TEST123'));
    }

    #[TestWith(['local'])]
    #[TestWith(['testing'])]
    #[TestWith(['staging'])]
    public function test_non_production_pages_do_not_load_analytics(string $environment): void
    {
        $this->app->instance('env', $environment);
        config(['services.google_analytics.enabled' => true, 'services.google_analytics.measurement_id' => 'G-TEST123']);

        $this->get('/projects')->assertOk()->assertDontSee('googletagmanager.com', false);
    }

    #[TestWith([false, 'G-TEST123'])]
    #[TestWith([true, null])]
    #[TestWith([true, ''])]
    #[TestWith([true, 'invalid'])]
    #[TestWith([true, 'G-TEST<script>'])]
    public function test_disabled_or_invalid_configuration_does_not_load_analytics(bool $enabled, ?string $measurementId): void
    {
        $this->app->instance('env', 'production');
        config(['services.google_analytics.enabled' => $enabled, 'services.google_analytics.measurement_id' => $measurementId]);

        $this->get('/projects')->assertOk()->assertDontSee('googletagmanager.com', false);
    }
}
