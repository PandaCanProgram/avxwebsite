<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_empty_state_when_there_is_no_feedback(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('No feedback yet');
        $response->assertSee('Add Your Feedback');
    }

    public function test_homepage_shows_submitted_feedback(): void
    {
        $testimonial = Testimonial::factory()->create(['role' => 'Drone Client']);

        $response = $this->get(route('home'));

        $response->assertSee($testimonial->name);
        $response->assertSee($testimonial->message);
        $response->assertSee('Drone Client');
        $response->assertDontSee('No feedback yet');
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
