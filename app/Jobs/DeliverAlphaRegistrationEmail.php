<?php

namespace App\Jobs;

use App\Mail\AlphaRegistrationNotification;
use App\Models\AlphaRegistration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class DeliverAlphaRegistrationEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 30;

    public function __construct(public string $registrationId)
    {
        $this->onConnection('database');
        $this->onQueue('inquiries');
    }

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300, 900, 3600];
    }

    /** @return list<WithoutOverlapping> */
    public function middleware(): array
    {
        return [(new WithoutOverlapping('alpha:'.$this->registrationId))->releaseAfter(15)->expireAfter(90)];
    }

    public function handle(): void
    {
        $registration = AlphaRegistration::findOrFail($this->registrationId);

        if ($registration->team_notified_at !== null) {
            return;
        }

        $sentMessage = Mail::mailer(config('inquiries.mailer'))
            ->to(config('inquiries.recipient'))
            ->send(new AlphaRegistrationNotification($registration));

        if ($sentMessage === null) {
            throw new RuntimeException('The alpha registration email was not accepted by the mail transport.');
        }

        $registration->forceFill(['team_notified_at' => now()])->save();
    }
}
