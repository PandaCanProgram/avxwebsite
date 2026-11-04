<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_only_featured_projects_with_link_to_all_projects(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('projects'));

        foreach (config('portfolio.projects') as $project) {
            if ($project['featured'] ?? false) {
                $response->assertSee($project['title']);
            } else {
                $response->assertDontSee($project['title']);
            }
        }
    }

    public function test_projects_page_lists_every_project(): void
    {
        $response = $this->get(route('projects'));

        $response->assertOk();

        foreach (config('portfolio.projects') as $project) {
            $response->assertSee($project['title']);
        }
    }

    public function test_projects_with_a_gallery_open_the_photo_viewer(): void
    {
        $response = $this->get(route('projects'));

        $response->assertSee('data-gallery-dialog', false);

        foreach (config('portfolio.projects') as $project) {
            if (isset($project['gallery'])) {
                $response->assertSee('View '.count($project['gallery']).' photos of '.$project['title']);
            }
        }
    }

    public function test_rescue_drone_uses_a_cutout_cover_with_its_four_original_gallery_photos(): void
    {
        $response = $this->get(route('projects'));
        $response->assertOk();
        $response->assertSee('AeroTech 737R Pixhawk and Raspberry Pi-Based Rescue Drone');
        $response->assertSee('FPV and thermal cameras for human detection');

        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $button = $xpath->query('//button[@data-gallery-open and starts-with(@data-gallery-title, "AeroTech 737R")]')->item(0);

        $this->assertNotNull($button);
        $this->assertStringStartsWith('View 4 photos of AeroTech 737R', $button->getAttribute('aria-label'));
        $this->assertFalse($button->hasAttribute('data-gallery-cutout'));
        $this->assertTrue($button->parentNode->hasAttribute('data-project-cutout'));
        $this->assertCount(4, json_decode($button->getAttribute('data-gallery-images'), true, flags: JSON_THROW_ON_ERROR));

        $existingCutoutGallery = $xpath->query('//button[@data-gallery-title="Camera Drone CAD Design"]')->item(0);
        $this->assertTrue($existingCutoutGallery->hasAttribute('data-gallery-cutout'));
    }
}
