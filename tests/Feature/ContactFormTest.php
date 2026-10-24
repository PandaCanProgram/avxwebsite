<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_offers_viber_gmail_and_facebook_options(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-quote-form', false);
        $response->assertSee('Where should we send it?');
        $response->assertSeeInOrder(['data-send-via="viber"', 'data-send-via="gmail"', 'data-send-via="facebook"'], false);
    }
}
