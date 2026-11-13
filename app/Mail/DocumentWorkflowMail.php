<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Form;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One email per document-workflow step (System Procedures, Support Documents and Forms).
 * Which step it is decides the subject, the wording, and whether the recipient is being
 * asked to act or just kept informed.
 */
class DocumentWorkflowMail extends Mailable
{
    use Queueable, SerializesModels;

    // The recipient has to act on the document.
    public const REVIEW_REQUESTED = 'review_requested';

    public const APPROVAL_REQUESTED = 'approval_requested';

    public const CODE_REQUESTED = 'code_requested';

    // The process owner is told how their document did.
    public const REVIEW_PASSED = 'review_passed';

    public const REVIEW_FAILED = 'review_failed';

    public const APPROVED = 'approved';

    public const NOT_APPROVED = 'not_approved';

    public const SENT_BACK_BY_REVIEWER = 'sent_back_by_reviewer';

    public const SENT_BACK_BY_APPROVER = 'sent_back_by_approver';

    public function __construct(
        public User $recipient,
        public Model $document,
        public string $event,
        public ?string $reason = null,
        public ?User $actor = null,
    ) {
        $this->actor ??= auth()->user();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->headline().': '.$this->documentLabel());
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.document-workflow',
            with: [
                'recipient' => $this->recipient,
                'headline' => $this->headline(),
                'documentType' => $this->documentType(),
                'documentLabel' => $this->documentLabel(),
                'body' => $this->body(), // not "message": mail views reserve $message for the Message object
                'reason' => filled($this->reason) ? trim($this->reason) : null,
                'url' => $this->documentUrl(),
                'buttonLabel' => $this->buttonLabel(),
            ],
        );
    }

    private function headline(): string
    {
        return match ($this->event) {
            self::REVIEW_REQUESTED => 'Document for Review',
            self::APPROVAL_REQUESTED => 'Document for Approval',
            self::CODE_REQUESTED => 'Document Ready for Code Assignment',
            self::REVIEW_PASSED => 'Document Passed Review',
            self::REVIEW_FAILED => 'Document Did Not Pass Review',
            self::APPROVED => 'Document Approved',
            self::NOT_APPROVED => 'Document Not Approved',
            self::SENT_BACK_BY_REVIEWER, self::SENT_BACK_BY_APPROVER => 'Document Sent Back for Revision',
        };
    }

    private function body(): string
    {
        $by = $this->actor ? ' by '.trim($this->actor->first_name.' '.$this->actor->last_name) : '';

        return match ($this->event) {
            self::REVIEW_REQUESTED => "A {$this->documentType()} has been sent to you for review{$by}.",
            self::APPROVAL_REQUESTED => "A {$this->documentType()} has passed review and is waiting for your approval.",
            self::CODE_REQUESTED => "A {$this->documentType()} has been approved and is waiting for a document code to be assigned.",
            self::REVIEW_PASSED => "Your {$this->documentType()} passed review{$by} and has been sent for approval.",
            self::REVIEW_FAILED => "Your {$this->documentType()} did not pass review{$by}.",
            self::APPROVED => "Your {$this->documentType()} has been approved{$by}. It will become active once the Document Controller assigns its code.",
            self::NOT_APPROVED => "Your {$this->documentType()} was not approved{$by}.",
            self::SENT_BACK_BY_REVIEWER => "Your {$this->documentType()} was sent back{$by} (reviewer) with comments to revise.",
            self::SENT_BACK_BY_APPROVER => "Your {$this->documentType()} was sent back{$by} (approver) with comments to revise.",
        };
    }

    private function buttonLabel(): string
    {
        return match ($this->event) {
            self::REVIEW_REQUESTED => 'Review Document',
            self::APPROVAL_REQUESTED => 'Approve Document',
            self::CODE_REQUESTED => 'Assign Code',
            default => 'View Document',
        };
    }

    private function documentType(): string
    {
        return match (true) {
            $this->document instanceof Document => 'System Procedure',
            $this->document instanceof SupportDocument => 'Support Document',
            $this->document instanceof Form => 'Form',
            default => 'document',
        };
    }

    private function documentLabel(): string
    {
        $code = trim((string) $this->document->code);

        return $this->document->title.($code !== '' ? " ({$code})" : '');
    }

    private function documentUrl(): string
    {
        return match (true) {
            $this->document instanceof Document => route('document.system_procedures.view_pdf', $this->document),
            $this->document instanceof SupportDocument => route('document.support_document.view', $this->document),
            $this->document instanceof Form => route('document.forms.view', $this->document),
        };
    }
}
