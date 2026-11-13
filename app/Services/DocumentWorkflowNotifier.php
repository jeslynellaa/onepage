<?php

namespace App\Services;

use App\Mail\DocumentWorkflowMail;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

/**
 * Emails the people concerned at each step of the document workflow, using the document's
 * section roles (process owner, reviewer, approver) and the company's Document Controllers.
 *
 * Call it after the transition has been committed: a failed email never undoes the transition,
 * it's only recorded so the controller can warn the user (see warnIfUnsent()).
 */
class DocumentWorkflowNotifier
{
    /** @var list<string> */
    private array $unsent = [];

    public function sentForReview(Model $doc): void
    {
        $section = $this->section($doc);

        $this->send($doc, [
            [$section?->reviewer, DocumentWorkflowMail::REVIEW_REQUESTED],
        ]);
    }

    public function reviewed(Model $doc, bool $passed, ?string $reason = null): void
    {
        $section = $this->section($doc);

        $this->send($doc, $passed
            ? [
                [$section?->approver, DocumentWorkflowMail::APPROVAL_REQUESTED],
                [$section?->processOwner, DocumentWorkflowMail::REVIEW_PASSED],
            ]
            : [
                [$section?->processOwner, DocumentWorkflowMail::REVIEW_FAILED],
            ], $reason);
    }

    public function approvalDecided(Model $doc, bool $approved, ?string $reason = null): void
    {
        $section = $this->section($doc);

        if (! $approved) {
            $this->send($doc, [[$section?->processOwner, DocumentWorkflowMail::NOT_APPROVED]], $reason);

            return;
        }

        $controllers = User::where('company_id', $doc->company_id)
            ->where('role', 'Document Controller')
            ->get()
            ->map(fn (User $user) => [$user, DocumentWorkflowMail::CODE_REQUESTED])
            ->all();

        $this->send($doc, [
            ...$controllers,
            [$section?->processOwner, DocumentWorkflowMail::APPROVED],
        ]);
    }

    /** @param  'review'|'approval'  $stage */
    public function sentBack(Model $doc, string $stage): void
    {
        $this->send($doc, [[
            $this->section($doc)?->processOwner,
            $stage === 'approval' ? DocumentWorkflowMail::SENT_BACK_BY_APPROVER : DocumentWorkflowMail::SENT_BACK_BY_REVIEWER,
        ]]);
    }

    /**
     * Adds a warning naming anyone who couldn't be emailed, alongside the action's success message.
     */
    public function warnIfUnsent(RedirectResponse $redirect): RedirectResponse
    {
        if ($this->unsent === []) {
            return $redirect;
        }

        return $redirect->withErrors([
            'email' => 'The notification email to '.implode(', ', $this->unsent).' could not be sent. Please let them know directly.',
        ]);
    }

    /**
     * Recipients are listed with "please act" emails first, so someone holding two roles on the
     * section gets the one that asks them to do something. Nobody is emailed about their own action.
     *
     * @param  list<array{0: ?User, 1: string}>  $recipients
     */
    private function send(Model $doc, array $recipients, ?string $reason = null): void
    {
        $actor = auth()->user();
        $seen = $actor ? [$actor->id => true] : [];

        foreach ($recipients as [$user, $event]) {
            if (! $user || blank($user->email) || isset($seen[$user->id])) {
                continue;
            }
            $seen[$user->id] = true;

            try {
                Mail::to($user)->send(new DocumentWorkflowMail($user, $doc, $event, $reason, $actor));
            } catch (\Throwable $e) {
                report($e);
                $this->unsent[] = $user->email;
            }
        }
    }

    private function section(Model $doc): ?Section
    {
        return $doc->section()->with(['processOwner', 'reviewer', 'approver'])->first();
    }
}
