<?php

namespace App\Mail;

use App\Models\AlphaRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlphaRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AlphaRegistration $registration) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->registration->email, $this->registration->name ?? '')],
            subject: 'AVX alpha testing: new pre-registration',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.alpha-registration');
    }
}
