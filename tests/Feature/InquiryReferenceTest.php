<?php

namespace Tests\Feature;

use App\Jobs\DeliverInquiryEmail;
use App\Mail\InquiryAcknowledgment;
use App\Mail\InquiryNotification;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class InquiryReferenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Validator::fakeDnsLookups();
    }

    protected function tearDown(): void
    {
        Validator::fakeDnsLookups(false);
        parent::tearDown();
    }

    #[TestWith(['contact'])]
    #[TestWith(['investment'])]
    public function test_new_inquiries_share_a_persisted_four_letter_reference_in_the_response_and_both_emails(string $kind): void
    {
        $payload = $this->payload($kind);
        $response = $this->postJson(route('inquiries.'.$kind), $payload)->assertCreated();
        $inquiry = Inquiry::sole();
        $reference = $response->json('reference');

        $this->assertMatchesRegularExpression('/\AAVX-[A-Z]{4}\z/', $reference);
        $this->assertSame($reference, $inquiry->reference());
        $this->assertDatabaseHas('inquiries', ['id' => $inquiry->id, 'reference_code' => substr($reference, 4)]);
        (new InquiryNotification($inquiry))
            ->assertHasSubject('['.$reference.'] '.($kind === 'investment' ? 'Investor inquiry: ' : 'Project inquiry: ').$inquiry->subject)
            ->assertSeeInHtml($reference);
        (new InquiryAcknowledgment($inquiry))
            ->assertHasSubject('['.$reference.'] We have received your inquiry | AVX Engineering Solutions')
            ->assertSeeInHtml($reference);

        $this->postJson(route('inquiries.'.$kind), $payload)->assertOk()->assertJsonPath('reference', $reference);
        $inquiry->update(['organization' => 'Updated organization']);
        $this->assertSame($reference, $inquiry->fresh()->reference());
        Queue::assertPushed(DeliverInquiryEmail::class, 2);
    }

    public function test_reference_collision_retries_without_losing_the_inquiry_or_duplicating_emails(): void
    {
        $existing = Inquiry::factory()->create();
        DB::table('inquiries')->where('id', $existing->id)->update(['reference_code' => 'ABCD']);
        $attempts = 0;
        Inquiry::creating(function (Inquiry $inquiry) use (&$attempts): void {
            $attempts++;
            if ($attempts === 1) {
                $inquiry->reference_code = 'ABCD';
            }
        });

        $response = $this->postJson(route('inquiries.contact'), $this->payload('contact'))->assertCreated();

        $this->assertSame(2, $attempts);
        $this->assertNotSame('AVX-ABCD', $response->json('reference'));
        $this->assertMatchesRegularExpression('/\AAVX-[A-Z]{4}\z/', $response->json('reference'));
        $this->assertDatabaseCount('inquiries', 2);
        Queue::assertPushed(DeliverInquiryEmail::class, 2);
    }

    public function test_inquiries_created_before_the_change_keep_their_original_reference(): void
    {
        $inquiry = Inquiry::factory()->create();
        DB::table('inquiries')->where('id', $inquiry->id)->update(['reference_code' => null]);

        $this->assertSame('AVX-'.$inquiry->id, $inquiry->fresh()->reference());
    }

    public function test_form_confirmation_uses_the_same_short_reference(): void
    {
        $response = $this->post(route('inquiries.contact'), $this->payload('contact'));

        $response->assertRedirect();
        $response->assertSessionHas('inquirySubmitted', fn (string $message): bool => str_contains($message, 'Reference: '.Inquiry::sole()->reference()));
        $this->assertMatchesRegularExpression('/\AAVX-[A-Z]{4}\z/', Inquiry::sole()->reference());
    }

    /** @return array<string, string> */
    private function payload(string $kind): array
    {
        $payload = array_merge(Inquiry::factory()->make()->only(['submission_id', 'name', 'email', 'message']), [
            'source' => $kind === 'investment' ? 'investment' : 'home',
        ]);

        if ($kind === 'investment') {
            $payload['interest'] = config('inquiries.interest_areas')[0];
        } else {
            $payload['subject'] = 'Custom drone fabrication';
        }

        return $payload;
    }
}
