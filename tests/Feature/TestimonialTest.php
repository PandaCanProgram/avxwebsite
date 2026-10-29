<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_static_feedback_when_database_has_no_feedback(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Zsann');
        $response->assertSee('Excellent support in custom drone flight controls integration and system optimization.');
        $response->assertSee('Aeronautical Engineering Student');
        $response->assertSee('Joshua');
        $response->assertSee('Highly recommended for FEA and thesis consultation.');
        $response->assertSee('Mechanical Engineering Student');
        $response->assertDontSee('Client feedback will be shared here soon.');
        $response->assertDontSee('Add Your Feedback');
        $response->assertDontSee('data-feedback-form', false);
        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_homepage_keeps_static_feedback_when_database_feedback_exists(): void
    {
        $testimonial = Testimonial::factory()->create(['name' => 'Database-only reviewer', 'role' => 'Drone Client']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Zsann');
        $response->assertSee('Joshua');
        $response->assertDontSee($testimonial->name);
        $response->assertDontSee($testimonial->message);
        $response->assertDontSee('Add Your Feedback');
    }

    public function test_visitor_can_submit_feedback(): void
    {
        $response = $this->post(route('testimonials.store'), [
            'name' => 'Maria S.',
            'role' => 'Thesis Student',
            'message' => 'The CFD report was clear and delivered on time.',
            'rating' => 5,
        ]);

        $response->assertRedirect(route('home').'#testimonials');
        $response->assertSessionHas('testimonialSubmitted');

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Maria S.',
            'role' => 'Thesis Student',
            'rating' => 5,
        ]);
    }

    public function test_feedback_requires_name_message_and_valid_rating(): void
    {
        $response = $this->post(route('testimonials.store'), [
            'name' => '',
            'message' => 'Too short',
            'rating' => 6,
        ]);

        $response->assertRedirect(route('home').'#testimonials');
        $response->assertSessionHasErrors(['name', 'message', 'rating'], null, 'testimonial');
        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_feedback_with_filled_honeypot_is_rejected(): void
    {
        $response = $this->post(route('testimonials.store'), [
            'name' => 'Spam Bot',
            'message' => 'Buy cheap things at my website today.',
            'rating' => 5,
            'website' => 'https://spam.example',
        ]);

        $response->assertSessionHasErrors(['website'], null, 'testimonial');
        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_feedback_submissions_are_rate_limited(): void
    {
        $feedback = [
            'name' => 'Repeat Visitor',
            'message' => 'Submitting the same feedback again and again.',
            'rating' => 4,
        ];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('testimonials.store'), $feedback)->assertRedirect();
        }

        $this->post(route('testimonials.store'), $feedback)->assertTooManyRequests();
    }
}
