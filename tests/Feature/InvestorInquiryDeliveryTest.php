<?php

namespace Tests\Feature;

use App\Jobs\DeliverInquiryEmail;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Tests\TestCase;

class InvestorInquiryDeliveryTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['contact'])]
    #[TestWith(['investment'])]
    public function test_submitted_inquiry_builds_two_complete_emails_with_correct_transport_recipients(string $kind): void
    {
        $this->useInMemoryMailTransport();
        Log::spy();
        Queue::fake();
        Validator::fakeDnsLookups();
        $payload = Inquiry::factory()->make(['email' => 'visitor@example.com'])->only([
            'submission_id', 'name', 'email', 'message',
        ]);
        $payload += [
            'source' => $kind === 'investment' ? 'investment' : 'home',
            'organization' => 'Investor research group',
            'phone' => '+63 917 123 4567',
            'inquiry_extra' => '',
        ];
        if ($kind === 'investment') {
            $payload['interest'] = 'Engineering & product design';
        } else {
            $payload['subject'] = 'Custom drone fabrication';
        }

        $this->postJson(route('inquiries.'.$kind), $payload)->assertCreated();
        Queue::assertPushed(DeliverInquiryEmail::class, 2);
        $jobs = Queue::pushed(DeliverInquiryEmail::class);
        foreach ($jobs as $job) {
            unserialize(serialize($job))->handle();
        }

        $transport = Mail::mailer('inquiry-review')->getSymfonyTransport();
        $this->assertInstanceOf(ArrayTransport::class, $transport);
        $this->assertCount(2, $transport->messages());
        [$teamMessage, $acknowledgment] = $transport->messages()->all();
        $inquiry = Inquiry::sole();

        $this->assertSame('info@avxengineering.com', $teamMessage->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertCount(1, $teamMessage->getEnvelope()->getRecipients());
        $this->assertSame($payload['email'], $acknowledgment->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertCount(1, $acknowledgment->getEnvelope()->getRecipients());
        $this->assertSame($payload['email'], $teamMessage->getOriginalMessage()->getReplyTo()[0]->getAddress());
        $this->assertSame('info@avxengineering.com', $acknowledgment->getOriginalMessage()->getReplyTo()[0]->getAddress());

        foreach ([$teamMessage, $acknowledgment] as $sentMessage) {
            $this->assertSame('info@avxengineering.com', $sentMessage->getEnvelope()->getSender()->getAddress());
            $this->assertStringContainsString($inquiry->reference(), $sentMessage->getOriginalMessage()->getHtmlBody());
            $this->assertStringContainsString($inquiry->reference(), $sentMessage->getOriginalMessage()->getTextBody());
            $this->assertStringContainsString('multipart/alternative', $sentMessage->toString());
        }

        $this->assertStringContainsString('Investor research group', $teamMessage->getOriginalMessage()->getHtmlBody());
        $this->assertStringContainsString($payload['phone'], $teamMessage->getOriginalMessage()->getHtmlBody());
        if ($kind === 'investment') {
            $this->assertSame('['.$inquiry->reference().'] Investor inquiry: Investment inquiry', $teamMessage->getOriginalMessage()->getSubject());
            $this->assertStringContainsString('Engineering &amp; product design', $teamMessage->getOriginalMessage()->getHtmlBody());
        }
        $this->assertStringContainsString('We have received your inquiry', $acknowledgment->getOriginalMessage()->getHtmlBody());
        $this->assertNotSame($teamMessage->getMessageId(), $acknowledgment->getMessageId());
        $this->assertNotNull($inquiry->team_notified_at);
        $this->assertNotNull($inquiry->acknowledged_at);
        Log::shouldHaveReceived('info')->with('Inquiry email accepted by mail transport.', [
            'reference' => $inquiry->reference(),
            'kind' => $kind,
            'delivery' => 'team',
            'mailer' => 'inquiry-review',
            'transport_message_id' => $teamMessage->getMessageId(),
        ])->once();
        Log::shouldHaveReceived('info')->with('Inquiry email accepted by mail transport.', [
            'reference' => $inquiry->reference(),
            'kind' => $kind,
            'delivery' => 'acknowledgment',
            'mailer' => 'inquiry-review',
            'transport_message_id' => $acknowledgment->getMessageId(),
        ])->once();

        foreach ($jobs as $job) {
            $job->handle();
        }
        $this->assertCount(2, $transport->messages());
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function test_canceled_mail_is_not_marked_as_sent(bool $acknowledgment): void
    {
        $this->useInMemoryMailTransport();
        $inquiry = Inquiry::factory()->create(['kind' => 'investment']);
        Event::listen(MessageSending::class, fn (MessageSending $event): bool => false);

        $this->expectException(RuntimeException::class);
        try {
            (new DeliverInquiryEmail($inquiry->id, $acknowledgment))->handle();
        } finally {
            $this->assertNull($inquiry->fresh()->team_notified_at);
            $this->assertNull($inquiry->fresh()->acknowledged_at);
            $this->assertCount(0, Mail::mailer('inquiry-review')->getSymfonyTransport()->messages());
        }
    }

    protected function tearDown(): void
    {
        Validator::fakeDnsLookups(false);
        parent::tearDown();
    }

    private function useInMemoryMailTransport(): void
    {
        config([
            'mail.mailers.inquiry-review' => ['transport' => 'array'],
            'inquiries.mailer' => 'inquiry-review',
            'inquiries.recipient' => 'info@avxengineering.com',
            'mail.from.address' => 'info@avxengineering.com',
            'mail.from.name' => 'AVX Engineering Solutions',
        ]);
    }
}
