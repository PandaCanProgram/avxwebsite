<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    #[TestWith(['/'])]
    #[TestWith(['/projects'])]
    #[TestWith(['/investment'])]
    public function test_inquiries_submit_to_backend_without_the_channel_picker(string $path): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertDontSee('Where should we send it?');
        $response->assertDontSee('data-send-via-dialog', false);
        $response->assertDontSee('Online sending is temporarily unavailable.');
        $response->assertSee('Your inquiry goes directly to our team.');
        $response->assertSee('mailto:info@avxengineering.com', false);
        $response->assertSee('tel:+639349170567', false);

        $document = new DOMDocument;
        $this->assertTrue($document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING));
        $xpath = new DOMXPath($document);
        $forms = $xpath->query('//form[@data-inquiry-form]');
        $this->assertGreaterThan(0, $forms->length);
        $this->assertSame(0, $xpath->query('//form[starts-with(@action, "mailto:")]')->length);

        foreach ($forms as $form) {
            $this->assertSame(route($path === '/investment' ? 'inquiries.investment' : 'inquiries.contact'), $form->getAttribute('action'));
            $this->assertSame(0, $xpath->query('.//fieldset[@disabled]', $form)->length);
            $this->assertSame(0, $xpath->query('.//button[@type="submit" and @disabled]', $form)->length);
            $this->assertSame(1, $xpath->query('.//input[@name="_token"]', $form)->length);
            $this->assertSame(1, $xpath->query('.//input[@name="submission_id" and string-length(@value) = 36]', $form)->length);
            $this->assertSame(1, $xpath->query('.//input[@name="website" and @tabindex="-1"]', $form)->length);
            $this->assertSame(1, $xpath->query('.//*[@data-inquiry-status and @role="status"]', $form)->length);
        }
    }
}
