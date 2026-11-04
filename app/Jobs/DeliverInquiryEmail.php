<?php

namespace App\Jobs;

use App\Mail\InquiryAcknowledgment;
use App\Mail\InquiryNotification;
use App\Models\Inquiry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

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

        $sentMessage = Mail::mailer(config('inquiries.mailer'))
            ->to($this->acknowledgment ? $inquiry->email : config('inquiries.recipient'))
            ->send($this->acknowledgment ? new InquiryAcknowledgment($inquiry) : new InquiryNotification($inquiry));

        if ($sentMessage === null) {
            throw new RuntimeException('The inquiry email was not accepted by the mail transport.');
        }

        $inquiry->forceFill([$sentAt => now()])->save();

        Log::info('Inquiry email accepted by mail transport.', [
            'reference' => $inquiry->reference(),
            'kind' => $inquiry->kind,
            'delivery' => $this->acknowledgment ? 'acknowledgment' : 'team',
            'mailer' => config('inquiries.mailer'),
            'transport_message_id' => $sentMessage->getMessageId(),
        ]);
    }
}
