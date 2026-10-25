<?php

namespace App\Mail;

use App\Models\Company;
use App\Services\DemoProvisioner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a prospect from Admin → Demos: when their demo workspace opens and
 * closes, and the link that gets them in.
 */
class DemoInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /** Where prospects are asked to reply to proceed or ask questions. */
    public const CONTACT_EMAIL = 'info.fcusi@gmail.com';

    public string $link;

    public function __construct(public Company $demo, public ?string $recipientName = null)
    {
        $this->link = DemoProvisioner::linkFor($demo);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address(self::CONTACT_EMAIL, 'OnePage by FCU')],
            subject: 'Your OnePage demo is ready: opens '.$this->demo->demo_starts_at->format('l, M j \a\t g:i A'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mails.demo-invitation');
    }
}
