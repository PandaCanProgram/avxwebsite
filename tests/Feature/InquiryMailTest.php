<?php

namespace Tests\Feature;

use App\Jobs\DeliverInquiryEmail;
use App\Mail\InquiryAcknowledgment;
use App\Mail\InquiryNotification;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class InquiryMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['mail.from.address' => 'info@avxengineering.com', 'mail.from.name' => 'AVX Engineering Solutions']);
        Mail::fake();
    }

    public function test_team_notification_goes_to_avx_with_visitor_reply_to(): void
    {
        $inquiry = Inquiry::factory()->create();
        $job = new DeliverInquiryEmail($inquiry->id);
        $job->handle();
        $job->handle();

        Mail::assertSent(InquiryNotification::class, function (InquiryNotification $mail) use ($inquiry): bool {
            $this->assertSame('info@avxengineering.com', $mail->envelope()->from->address);
            $this->assertSame($inquiry->email, $mail->envelope()->replyTo[0]->address);
            $this->assertSame($inquiry->name, $mail->envelope()->replyTo[0]->name);

            return $mail->hasTo('info@avxengineering.com') && $mail->inquiry->is($inquiry);
        });
        Mail::assertSentCount(1);
        $this->assertNotNull($inquiry->fresh()->team_notified_at);
        $this->assertNull($inquiry->fresh()->acknowledged_at);
    }

    public function test_visitor_receives_acknowledgment_from_avx_without_reflecting_their_message(): void
    {
        $inquiry = Inquiry::factory()->create(['message' => 'Do not reflect this private message in the acknowledgment.']);
        $job = new DeliverInquiryEmail($inquiry->id, true);
        $job->handle();
        $job->handle();

        Mail::assertSent(InquiryAcknowledgment::class, function (InquiryAcknowledgment $mail) use ($inquiry): bool {
            $this->assertSame('info@avxengineering.com', $mail->envelope()->from->address);
            $this->assertSame('info@avxengineering.com', $mail->envelope()->replyTo[0]->address);
            $mail->assertSeeInHtml('We have received your inquiry');
            $mail->assertSeeInHtml($inquiry->reference());
            $mail->assertDontSeeInHtml($inquiry->message);
            $mail->assertDontSeeInHtml('Laravel Logo');

            return $mail->hasTo($inquiry->email);
        });
        Mail::assertSentCount(1);
        $this->assertNotNull($inquiry->fresh()->acknowledged_at);
        $this->assertNull($inquiry->fresh()->team_notified_at);
    }

    public function test_mail_failure_keeps_delivery_pending_for_retry(): void
    {
        $inquiry = Inquiry::factory()->create();
        Mail::shouldReceive('mailer')->with('smtp')->andReturnSelf();
        Mail::shouldReceive('to')->with('info@avxengineering.com')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP unavailable'));

        try {
            (new DeliverInquiryEmail($inquiry->id))->handle();
            $this->fail('Delivery failures must be retried by the queue.');
        } catch (RuntimeException $exception) {
            $this->assertSame('SMTP unavailable', $exception->getMessage());
        }

        $this->assertNull($inquiry->fresh()->team_notified_at);
        $this->assertNull($inquiry->fresh()->acknowledged_at);
    }

    public function test_team_email_escapes_user_html_and_contains_the_project_brief(): void
    {
        $inquiry = Inquiry::factory()->create([
            'organization' => 'Research organization',
            'message' => '<img src=x onerror=alert(1)> Please discuss our drone project.',
        ]);
        $mail = new InquiryNotification($inquiry);

        $mail->assertSeeInHtml($inquiry->reference());
        $mail->assertSeeInHtml('Research organization');
        $mail->assertSeeInHtml('&lt;img src=x onerror=alert(1)&gt;', false);
        $mail->assertDontSeeInHtml('<img src=x', false);
    }
}
