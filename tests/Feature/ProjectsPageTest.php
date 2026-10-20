<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProjectsPageTest extends TestCase
{
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
}
