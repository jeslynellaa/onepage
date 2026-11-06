<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A one-off message written by a host admin on Admin → Email Users and sent to
 * each selected user individually (so recipients never see each other).
 */
class AdminAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var list<string> */
    public array $paragraphs;

    public function __construct(
        public User $recipient,
        public string $emailSubject,
        string $body,
        public ?string $buttonLabel = null,
        public ?string $buttonUrl = null,
    ) {
        // Blank lines separate paragraphs; single line breaks are kept inside a paragraph.
        $this->paragraphs = array_values(array_filter(
            array_map('trim', preg_split('/\R\s*\R/', trim($body))),
            fn (string $p) => $p !== ''
        ));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address(DemoInvitationMail::CONTACT_EMAIL, 'OnePage by FCU')],
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mails.admin-announcement');
    }
}
