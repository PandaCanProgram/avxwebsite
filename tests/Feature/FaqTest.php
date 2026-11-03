<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class FaqTest extends TestCase
{
    public function test_all_faq_answers_start_closed(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $document = new DOMDocument;
        $this->assertTrue($document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING));
        $xpath = new DOMXPath($document);
        $this->assertGreaterThan(0, $xpath->query('//details[@data-faq-item]')->length);
        $this->assertSame(0, $xpath->query('//details[@data-faq-item and @open]')->length);
    }
}
