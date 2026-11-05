<?php

namespace Tests\Feature;

use App\Jobs\DeliverAlphaRegistrationEmail;
use App\Models\AlphaRegistration;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Tests\TestCase;

class AlphaRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['inquiries.alpha_registration_enabled' => true]);
        Validator::fakeDnsLookups();
        Mail::fake();
    }

    protected function tearDown(): void
    {
        Validator::fakeDnsLookups(false);
        parent::tearDown();
    }

    #[TestWith(['/'])]
    #[TestWith(['/projects'])]
    #[TestWith(['/investment'])]
    #[TestWith(['/drone-platforms'])]
    public function test_banner_is_directly_after_the_header_on_public_pages(string $path): void
    {
        $response = $this->get($path)->assertOk();
        $response->assertSeeInOrder(['</header>', 'id="alpha-registration"', 'name="alpha_email"'], false);
        $response->assertSee(route('alpha-registration.store'), false);
        $response->assertSee('data-alpha-registration-open', false);
        $response->assertSeeInOrder(['<dialog', 'name="alpha_name"', 'name="alpha_email"', '</dialog>'], false);
    }

    public function test_signup_saves_email_and_queues_only_the_team_notification(): void
    {
        Queue::fake();

        $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => ' Visitor@Example.com ', 'source' => 'home'])
            ->assertOk()->assertJsonPath('message', "You're on the list! We'll contact you when an alpha test is ready.");

        $registration = AlphaRegistration::sole();
        $this->assertSame('Visitor Name', $registration->name);
        $this->assertSame('visitor@example.com', $registration->email);
        $this->assertNotNull($registration->consented_at);
        $this->assertNull($registration->team_notified_at);
        Queue::assertPushed(DeliverAlphaRegistrationEmail::class, fn (DeliverAlphaRegistrationEmail $job): bool => $job->registrationId === $registration->id && $job->connection === 'database' && $job->queue === 'inquiries');
        Queue::assertPushedOn('inquiries', DeliverAlphaRegistrationEmail::class);
        Queue::assertCount(1);
        Mail::assertNothingSent();
    }

    public function test_duplicate_email_does_not_create_another_lead_or_email(): void
    {
        Queue::fake();
        $payload = ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'home'];
        $first = $this->postJson(route('alpha-registration.store'), $payload)->assertOk();
        $second = $this->postJson(route('alpha-registration.store'), array_merge($payload, ['alpha_name' => 'Visitor Name', 'alpha_email' => 'VISITOR@example.com']))->assertOk();

        $this->assertSame($first->json(), $second->json());
        $this->assertDatabaseCount('alpha_registrations', 1);
        Queue::assertCount(1);
    }

    #[TestWith(['alpha_email', 'not-an-email'])]
    #[TestWith(['alpha_name', ''])]
    #[TestWith(['alpha_name', ['unexpected']])]
    #[TestWith(['alpha_name', "Visitor\r\nBcc: other@example.com"])]
    #[TestWith(['alpha_email', ''])]
    #[TestWith(['alpha_email', ['unexpected']])]
    #[TestWith(['alpha_email', "visitor@example.com\r\nBcc: other@example.com"])]
    #[TestWith(['alpha_website', 'spam'])]
    #[TestWith(['source', 'https://untrusted.example'])]
    public function test_invalid_signups_do_not_save_or_queue(string $field, mixed $value): void
    {
        Queue::fake();
        $this->postJson(route('alpha-registration.store'), array_merge(
            ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'home'], [$field => $value],
        ))->assertUnprocessable()->assertJsonValidationErrors($field);

        $this->assertDatabaseCount('alpha_registrations', 0);
        Queue::assertNothingPushed();
    }

    public function test_switch_hides_banner_and_closes_submissions_without_removing_leads(): void
    {
        $registration = AlphaRegistration::factory()->create();
        config(['inquiries.alpha_registration_enabled' => false]);
        Queue::fake();

        foreach (['/', '/projects', '/investment', '/drone-platforms'] as $path) {
            $this->get($path)->assertOk()->assertDontSee('data-alpha-registration-form', false);
        }

        $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'home'])->assertNotFound();
        $this->assertDatabaseCount('alpha_registrations', 1);
        $this->assertModelExists($registration);
        Queue::assertNothingPushed();
    }

    public function test_form_works_without_javascript_and_errors_use_a_separate_bag(): void
    {
        Queue::fake();
        $this->post(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'projects'])
            ->assertRedirect(route('projects').'#alpha-registration')->assertSessionHas('alphaRegistrationSubmitted');

        $this->post(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'invalid', 'source' => 'projects'])
            ->assertRedirect(route('projects').'#alpha-registration')
            ->assertSessionHasErrors(['alpha_email'], null, 'alphaRegistration');
    }

    #[TestWith(['home'])]
    #[TestWith(['projects'])]
    public function test_invalid_submission_reopens_the_dialog_after_redirect(string $source): void
    {
        $page = $this->followingRedirects()->post(route('alpha-registration.store'), [
            'alpha_name' => 'Visitor Name', 'alpha_email' => 'invalid', 'source' => $source,
        ])->assertOk();
        $document = new DOMDocument;
        $document->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $dialog = (new DOMXPath($document))->query('//dialog[@data-alpha-registration-dialog]')->item(0);
        $this->assertNotNull($dialog);
        $this->assertTrue($dialog->hasAttribute('data-alpha-registration-restore'));
        $this->assertTrue($dialog->hasAttribute('open'));
        $page->assertDontSee('data-flight-intro', false);
    }

    public function test_email_limit_prevents_repeated_requests_but_allows_different_booth_visitors(): void
    {
        Queue::fake();
        for ($index = 0; $index < 3; $index++) {
            $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'home'])->assertOk();
        }
        $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'VISITOR@example.com', 'source' => 'home'])->assertTooManyRequests();
        $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'another@example.com', 'source' => 'home'])->assertOk();
        $this->assertDatabaseCount('alpha_registrations', 2);
        Queue::assertCount(2);
    }

    public function test_signup_and_database_notification_job_are_saved_together(): void
    {
        $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'home'])->assertOk();
        $this->assertDatabaseHas('jobs', ['queue' => 'inquiries']);
        $this->assertSame(1, DB::table('jobs')->count());
        Mail::assertNothingSent();
    }

    public function test_queue_failure_rolls_back_registration_so_a_retry_can_succeed(): void
    {
        $this->withoutExceptionHandling();
        Queue::shouldReceive('connection')->with('database')->andThrow(new RuntimeException('Queue unavailable'));

        try {
            $this->postJson(route('alpha-registration.store'), ['alpha_name' => 'Visitor Name', 'alpha_email' => 'visitor@example.com', 'source' => 'home']);
            $this->fail('Queue failure must not report a successful signup.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Queue unavailable', $exception->getMessage());
        }

        $this->assertDatabaseCount('alpha_registrations', 0);
    }
}
