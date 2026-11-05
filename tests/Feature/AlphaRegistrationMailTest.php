<?php

namespace Tests\Feature;

use App\Jobs\DeliverAlphaRegistrationEmail;
use App\Models\AlphaRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class AlphaRegistrationMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'mail.from.address' => 'info@avxengineering.com',
            'mail.from.name' => 'AVX Engineering Solutions',
            'inquiries.recipient' => 'info@avxengineering.com',
            'inquiries.mailer' => 'array',
        ]);
    }

    public function test_only_team_receives_an_email_even_after_retry_and_banner_shutdown(): void
    {
        $registration = AlphaRegistration::factory()->create();
        config(['inquiries.alpha_registration_enabled' => false]);
        $job = new DeliverAlphaRegistrationEmail($registration->id);
        $job->handle();
        $job->handle();

        $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $message = $messages->sole()->getOriginalMessage();
        $this->assertCount(1, $message->getTo());
        $this->assertSame('info@avxengineering.com', $message->getTo()[0]->getAddress());
        $this->assertSame('info@avxengineering.com', $message->getFrom()[0]->getAddress());
        $this->assertSame($registration->email, $message->getReplyTo()[0]->getAddress());
        $this->assertSame($registration->name, $message->getReplyTo()[0]->getName());
        $this->assertStringContainsString(e($registration->name), $message->getHtmlBody());
        $this->assertStringContainsString('alpha testing', $message->getSubject());
        $this->assertStringContainsString($registration->email, $message->getHtmlBody());
        $this->assertNotNull($registration->fresh()->team_notified_at);
    }

    public function test_failed_email_remains_pending_for_the_queue_to_retry(): void
    {
        $registration = AlphaRegistration::factory()->create();
        Mail::shouldReceive('mailer')->with('array')->andReturnSelf();
        Mail::shouldReceive('to')->with('info@avxengineering.com')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP unavailable'));

        try {
            (new DeliverAlphaRegistrationEmail($registration->id))->handle();
            $this->fail('Transport failures must be retried.');
        } catch (RuntimeException $exception) {
            $this->assertSame('SMTP unavailable', $exception->getMessage());
        }

        $this->assertNull($registration->fresh()->team_notified_at);
        $this->assertModelExists($registration);
    }
}
