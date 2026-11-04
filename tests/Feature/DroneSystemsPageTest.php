<?php

namespace Tests\Feature;

use App\Jobs\DeliverInquiryEmail;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class DroneSystemsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pricing_page_shows_the_four_indicative_packages(): void
    {
        $response = $this->get('/drone-platforms');

        $response->assertOk();
        $response->assertSee('Drone Packages &amp; Pricing | AVX Engineering Solutions', false);
        $response->assertSee('10 kg Lifter / Sprayer');
        $response->assertSee('20 kg Lifter / Sprayer');
        $response->assertSee('Development Kit — IP54');
        $response->assertSee('₱383,000');
        $response->assertSee('₱509,000');
        $response->assertSee('From ₱140,000');
        $response->assertSee('From ₱589,000');
        $response->assertSee('All amounts are indicative, not a final order total.');
        $response->assertDontSee('data-flight-intro', false);

        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $this->assertSame(4, $xpath->query('//article[@data-package-card]')->length);
        $this->assertSame(8, $xpath->query('//a[@data-package-inquire]')->length);
        $this->assertSame(1, $xpath->query('//form[@id="quote"]//input[@name="source" and @value="drone-systems"]')->length);
        $this->assertSame(2, $xpath->query('//header//a[@aria-current="page"]')->length);
    }

    public function test_selecting_a_package_prefills_a_safe_inquiry_subject_without_javascript(): void
    {
        $response = $this->get(route('drone-systems', ['package' => '20kg-lifter-sprayer']));

        $response->assertOk();
        $response->assertSee('value="Drone package inquiry: 20 kg Lifter / Sprayer"', false);
        $response->assertSee(route('drone-systems', ['package' => 'development-kit-ip54']).'#contact', false);
    }

    public function test_previous_address_permanently_redirects_and_preserves_package_selection(): void
    {
        $this->get('/drone-systems?package=20kg-lifter-sprayer')
            ->assertStatus(301)
            ->assertRedirect(route('drone-systems', ['package' => '20kg-lifter-sprayer']));
    }

    public function test_unknown_package_selection_does_not_inject_content_into_the_form(): void
    {
        $response = $this->get(route('drone-systems', ['package' => '<script>untrusted()</script>']));

        $response->assertOk();
        $response->assertSee('value="Drone package inquiry"', false);
        $response->assertDontSee('<script>untrusted()</script>', false);
    }

    public function test_package_inquiry_uses_the_existing_delivery_flow_and_returns_to_the_pricing_page(): void
    {
        Mail::fake();
        Queue::fake();
        Validator::fakeDnsLookups();

        try {
            $this->post(route('inquiries.contact'), [
                'submission_id' => (string) Str::uuid(),
                'source' => 'drone-systems',
                'name' => 'Package customer',
                'email' => 'customer@example.com',
                'subject' => 'Drone package inquiry: 10 kg Lifter / Sprayer',
                'message' => 'Please send a quotation for our agricultural drone project.',
                'inquiry_extra' => '',
            ])->assertRedirect(route('drone-systems').'#contact')->assertSessionHas('inquirySubmitted');

            $this->assertDatabaseHas('inquiries', [
                'kind' => 'contact',
                'subject' => 'Drone package inquiry: 10 kg Lifter / Sprayer',
                'email' => 'customer@example.com',
            ]);
            Queue::assertPushed(DeliverInquiryEmail::class, 2);
            Mail::assertNothingSent();
        } finally {
            Validator::fakeDnsLookups(false);
        }
    }

    public function test_invalid_package_inquiry_returns_validation_errors_to_the_pricing_page(): void
    {
        Queue::fake();

        $this->post(route('inquiries.contact'), ['source' => 'drone-systems'])
            ->assertRedirect(route('drone-systems').'#contact')
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message'], null, 'inquiry');
        Queue::assertNothingPushed();
    }
}
