<?php

namespace Tests\Feature;

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
}
