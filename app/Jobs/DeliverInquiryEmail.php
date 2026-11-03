<?php

namespace App\Jobs;

use App\Mail\InquiryAcknowledgment;
use App\Mail\InquiryNotification;
use App\Models\Inquiry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Mail;

class DeliverInquiryEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $inquiryId, public bool $acknowledgment = false)
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
        return [(new WithoutOverlapping($this->inquiryId.($this->acknowledgment ? ':acknowledgment' : ':team')))
            ->releaseAfter(15)->expireAfter(90)];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $inquiry = Inquiry::findOrFail($this->inquiryId);
        $sentAt = $this->acknowledgment ? 'acknowledged_at' : 'team_notified_at';

        if ($inquiry->{$sentAt} !== null) {
            return;
        }

        Mail::mailer(config('inquiries.mailer'))
            ->to($this->acknowledgment ? $inquiry->email : config('inquiries.recipient'))
            ->send($this->acknowledgment ? new InquiryAcknowledgment($inquiry) : new InquiryNotification($inquiry));

        $inquiry->forceFill([$sentAt => now()])->save();
    }
}
