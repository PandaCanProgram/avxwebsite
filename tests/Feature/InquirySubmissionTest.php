<?php

namespace Tests\Feature;

use App\Jobs\DeliverInquiryEmail;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Tests\TestCase;

class InquirySubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_contact_inquiry_is_saved_and_both_emails_are_queued(): void
    {
        Queue::fake();
        $payload = $this->contactPayload();
        $response = $this->postJson(route('inquiries.contact'), $payload);

        $response->assertCreated()->assertJsonStructure(['message', 'reference', 'next_submission_id']);
        $this->assertDatabaseHas('inquiries', [
            'submission_id' => $payload['submission_id'], 'kind' => 'contact',
            'email' => $payload['email'], 'organization' => 'Research organization',
            'message' => $payload['message'], 'team_notified_at' => null, 'acknowledged_at' => null,
        ]);
        $inquiry = Inquiry::sole();
        $response->assertJsonPath('reference', $inquiry->reference());
        $this->assertNotSame($payload['submission_id'], $response->json('next_submission_id'));
        Queue::assertPushed(DeliverInquiryEmail::class, fn (DeliverInquiryEmail $job): bool => $job->inquiryId === $inquiry->id && ! $job->acknowledgment && $job->connection === 'database' && $job->queue === 'inquiries');
        Queue::assertPushed(DeliverInquiryEmail::class, fn (DeliverInquiryEmail $job): bool => $job->inquiryId === $inquiry->id && $job->acknowledgment);
        Queue::assertPushed(DeliverInquiryEmail::class, 2);
        Mail::assertNothingSent();
    }

    public function test_investor_inquiry_uses_the_investment_type_and_details(): void
    {
        Queue::fake();
        $payload = $this->contactPayload();
        unset($payload['subject']);
        $payload = array_merge($payload, [
            'source' => 'investment', 'phone' => '+639001234567',
            'interest' => 'Engineering & product design', 'recipient' => 'other@example.test',
        ]);

        $this->postJson(route('inquiries.investment'), $payload)->assertCreated();

        $this->assertDatabaseHas('inquiries', [
            'kind' => 'investment', 'subject' => 'Investment inquiry',
            'phone' => $payload['phone'], 'interest' => $payload['interest'],
        ]);
        Queue::assertPushed(DeliverInquiryEmail::class, 2);
    }

    public function test_retrying_identical_submission_does_not_duplicate_inquiry_or_emails(): void
    {
        Queue::fake();
        $payload = $this->contactPayload();
        $first = $this->postJson(route('inquiries.contact'), $payload)->assertCreated();
        $retry = $this->postJson(route('inquiries.contact'), $payload)->assertOk();

        $this->assertSame($first->json('reference'), $retry->json('reference'));
        $this->assertDatabaseCount('inquiries', 1);
        Queue::assertPushed(DeliverInquiryEmail::class, 2);

        $this->postJson(route('inquiries.contact'), array_merge($payload, ['message' => 'A different inquiry using the old token.']))
            ->assertUnprocessable()->assertJsonValidationErrors('submission_id');
        $this->assertDatabaseCount('inquiries', 1);
        Queue::assertPushed(DeliverInquiryEmail::class, 2);
    }

    #[TestWith(['name', ''])]
    #[TestWith(['name', "Visitor\r\nBcc: other@example.test"])]
    #[TestWith(['email', 'not-an-email'])]
    #[TestWith(['email', ['unexpected-array']])]
    #[TestWith(['subject', "Quote\r\nBcc: other@example.test"])]
    #[TestWith(['message', 'Short'])]
    #[TestWith(['website', 'https://spam.example'])]
    #[TestWith(['submission_id', 'invalid-token'])]
    #[TestWith(['source', 'https://other.example'])]
    public function test_invalid_inquiries_do_not_save_or_send(string $field, mixed $value): void
    {
        Queue::fake();
        $this->postJson(route('inquiries.contact'), array_merge($this->contactPayload(), [$field => $value]))
            ->assertUnprocessable()->assertJsonValidationErrors($field);

        $this->assertDatabaseCount('inquiries', 0);
        Queue::assertNothingPushed();
        Mail::assertNothingSent();
    }

    public function test_oversized_messages_are_rejected(): void
    {
        Queue::fake();
        $this->postJson(route('inquiries.contact'), array_merge($this->contactPayload(), ['message' => str_repeat('a', 5001)]))
            ->assertUnprocessable()->assertJsonValidationErrors('message');

        $this->assertDatabaseCount('inquiries', 0);
        Queue::assertNothingPushed();
    }

    public function test_submission_rate_limit_rejects_excess_requests(): void
    {
        Queue::fake();
        for ($index = 0; $index < 5; $index++) {
            $this->postJson(route('inquiries.contact'), array_merge($this->contactPayload(), ['email' => "visitor{$index}@example.test"]))
                ->assertCreated();
        }

        $this->postJson(route('inquiries.contact'), $this->contactPayload())->assertTooManyRequests();
        $this->assertDatabaseCount('inquiries', 5);
        Queue::assertPushed(DeliverInquiryEmail::class, 10);
    }

    public function test_recipient_limit_applies_across_different_ips(): void
    {
        Queue::fake();
        for ($index = 1; $index <= 3; $index++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.'.$index])
                ->postJson(route('inquiries.contact'), $this->contactPayload())->assertCreated();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.4'])
            ->postJson(route('inquiries.contact'), $this->contactPayload())->assertTooManyRequests();
        $this->assertDatabaseCount('inquiries', 3);
    }

    public function test_jobs_are_persisted_without_running_smtp_during_submission(): void
    {
        $this->postJson(route('inquiries.contact'), $this->contactPayload())->assertCreated();

        $this->assertDatabaseCount('inquiries', 1);
        $this->assertDatabaseCount('jobs', 2);
        $this->assertSame(2, DB::table('jobs')->where('queue', 'inquiries')->count());
        Mail::assertNothingSent();
    }

    public function test_queue_failure_rolls_back_inquiry_instead_of_claiming_receipt(): void
    {
        Queue::shouldReceive('connection')->with('database')->andThrow(new RuntimeException('Queue unavailable'));
        $this->postJson(route('inquiries.contact'), $this->contactPayload())->assertServerError();

        $this->assertDatabaseCount('inquiries', 0);
        $this->assertDatabaseCount('jobs', 0);
        Mail::assertNothingSent();
    }

    public function test_html_submission_returns_to_its_form_with_confirmation(): void
    {
        Queue::fake();
        $this->post(route('inquiries.contact'), array_merge($this->contactPayload(), ['source' => 'projects']))
            ->assertRedirect(route('projects').'#contact')->assertSessionHas('inquirySubmitted');
        $this->assertDatabaseCount('inquiries', 1);
    }

    public function test_html_validation_preserves_input_and_returns_to_its_form(): void
    {
        Queue::fake();
        $payload = array_merge($this->contactPayload(), ['email' => 'invalid']);
        $this->post(route('inquiries.contact'), $payload)
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHasErrors(['email'], null, 'inquiry')
            ->assertSessionHasInput('message', $payload['message']);
        $this->assertDatabaseCount('inquiries', 0);
        Queue::assertNothingPushed();
    }

    /** @return array<string, string> */
    private function contactPayload(): array
    {
        return array_merge(Inquiry::factory()->make(['email' => 'visitor@example.test'])->only([
            'submission_id', 'name', 'email', 'subject', 'message',
        ]), ['source' => 'home', 'organization' => 'Research organization']);
    }
}
