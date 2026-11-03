<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultantAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $consultant, public Company $client) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You Have Been Assigned to '.$this->client->name.' on OnePage',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.consultant-assigned',
            with: [
                'consultant' => $this->consultant,
                'client' => $this->client,
            ],
        );
    }
}
