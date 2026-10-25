<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Document;
use App\Models\ProcedureComments;
use App\Models\ProcedureSteps;
use App\Models\Section;
use App\Models\StepDocuments;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Builds a self-contained, throwaway demo company: a cast of personas covering
 * every role in the document workflow, plus sample procedures sitting at each
 * workflow status, so a prospect can walk the whole flow alone.
 *
 * Document management only: Risk Management isn't released yet, so no risk data
 * is seeded and DemoGuard keeps demo personas out of the risk/SOAR routes.
 */
class DemoProvisioner
{
    /**
     * Reserved TLD (RFC 2606) — guaranteed undeliverable. SuppressDemoMail also
     * drops these recipients before anything is handed to the mail transport.
     */
    public const EMAIL_DOMAIN = 'demo.onepage.invalid';

    /**
     * The persona cast, keyed by role. Each role appears once so the switcher
     * can describe a persona from its role alone.
     */
    public const PERSONAS = [
        'Document Controller' => [
            'first_name' => 'Dana', 'last_name' => 'Cruz', 'position' => 'Document Controller',
            'blurb' => 'Assigns codes, activates documents, keeps the register',
        ],
        'User' => [
            'first_name' => 'Paolo', 'last_name' => 'Reyes', 'position' => 'Operations Manager',
            'blurb' => 'Process Owner: drafts procedures and sends them for review',
        ],
        'Reviewer' => [
            'first_name' => 'Rina', 'last_name' => 'Santos', 'position' => 'QMS Head',
            'blurb' => 'Reviews drafts, comments, sends back or passes',
        ],
        'Approver' => [
            'first_name' => 'Alex', 'last_name' => 'Mendoza', 'position' => 'General Manager',
            'blurb' => 'Approves or rejects reviewed documents',
        ],
        'Top Management' => [
            'first_name' => 'Tess', 'last_name' => 'Villanueva', 'position' => 'President',
            'blurb' => 'Oversight: dashboards, manuals and the document register',
        ],
        'Admin' => [
            'first_name' => 'Sam', 'last_name' => 'Lim', 'position' => 'System Administrator',
            'blurb' => 'Company settings and PDF branding',
        ],
    ];

    /** The persona a prospect lands as when they open their demo link. */
    public const LANDING_ROLE = 'Document Controller';

    private Company $company;

    /** "Now" from the prospect's point of view: sample history is dated back from when the demo opens. */
    private CarbonInterface $opensAt;

    /** @var array<string, User> keyed by role */
    private array $people = [];

    /** @var array<string, Section> keyed by short code */
    private array $sections = [];

    /** @var array<string, bool> table => has a company_id column */
    private array $tenantTables = [];

    public static function isDemoEmail(string $email): bool
    {
        return Str::endsWith(Str::lower($email), '@'.self::EMAIL_DOMAIN);
    }

    public static function blurbFor(string $role): ?string
    {
        return self::PERSONAS[$role]['blurb'] ?? null;
    }

    /**
     * The link a prospect is sent. It carries no expiry of its own: the company's
     * demo_starts_at/demo_expires_at decide when it works, so the link survives
     * an extension, and it 404s once demo:prune deletes the company.
     */
    public static function linkFor(Company $company): string
    {
        return URL::signedRoute('demo.enter', ['company' => $company]);
    }

    /**
     * @param  CarbonInterface|null  $startsAt  when the demo opens; null opens it now
     * @param  int  $hours  how long it stays open, counted from $startsAt
     */
    public function provision(?string $companyName = null, int $hours = 72, ?CarbonInterface $startsAt = null): Company
    {
        $this->opensAt = $startsAt && $startsAt->isFuture() ? $startsAt->copy() : now();

        // Model events are switched off for the whole build: the tenant models'
        // creating-hooks stamp company_id from CompanyContext, which would point at
        // the *caller's* company if this ever runs inside a logged-in request.
        // Every row below sets company_id explicitly instead.
        return DB::transaction(fn () => Model::withoutEvents(function () use ($companyName, $hours) {
            $expiresAt = $this->opensAt->copy()->addHours($hours);

            $this->company = Company::create([
                'name' => $companyName ?: 'Northwind Manufacturing (Demo)',
                'slug' => Company::generateUniqueSlug('demo-'.Str::random(8)),
                'subscription_plan' => 'demo',
                'subscription_status' => 'active',
                'subscription_ends_at' => $expiresAt,
                'is_demo' => true,
                'demo_starts_at' => $this->opensAt,
                'demo_expires_at' => $expiresAt,
                'hex_code' => '#1F6FEB',
            ]);

            $this->createPeople();
            $this->createSections();
            $this->createProcedures();

            return $this->company;
        }));
    }

    private function createPeople(): void
    {
        // Random, never-shown password: personas are only ever entered through the
        // signed demo link and the in-app switcher, never by typing credentials.
        $password = Str::random(40);

        foreach (self::PERSONAS as $role => $persona) {
            $handle = Str::lower($persona['first_name']).'.'.$this->company->id;

            $this->people[$role] = User::create([
                'company_id' => $this->company->id,
                'first_name' => $persona['first_name'],
                'last_name' => $persona['last_name'],
                'username' => 'demo_'.$handle,
                'email' => $handle.'@'.self::EMAIL_DOMAIN,
                'password' => $password,
                'role' => $role,
                'position' => $persona['position'],
                'active' => true,
            ]);
        }
    }

    private function createSections(): void
    {
        $titles = [
            'BPL' => 'Business Planning',
            'HRM' => 'Human Resource Management',
            'PUR' => 'Purchasing',
            'DIM' => 'Documented Information Management',
            'CIM' => 'Continual Improvement',
        ];

        $number = 1;
        foreach ($titles as $code => $title) {
            $this->sections[$code] = Section::create([
                'company_id' => $this->company->id,
                'manual' => 'System Procedures',
                'section_number' => str_pad($number++, 2, '0', STR_PAD_LEFT),
                'title' => $title,
                'description' => $code,
                // One owner/reviewer/approver chain across every section keeps the
                // walkthrough simple: Paolo → Rina → Alex → Dana.
                'process_owner_id' => $this->people['User']->id,
                'reviewer_id' => $this->people['Reviewer']->id,
                'approver_id' => $this->people['Approver']->id,
            ]);
        }
    }

    /**
     * One procedure per workflow status, each with the activity trail it would
     * have built up getting there.
     */
    private function createProcedures(): void
    {
        // Revision 0 → superseded by revision 1, so revision history has something to show.
        $this->procedure('DIM', 'SP-DIM-01', 'Control of Documented Information', 'Superseded', '0', 150, [
            ['created draft', 150], ['submitted for review', 148], ['review passed', 146, 'Reviewer'],
            ['approved', 145, 'Approver'], ['assigned code', 144, 'Document Controller'], ['superseded', 40, 'Approver'],
        ], effectiveDaysAgo: 144);

        $this->procedure('DIM', 'SP-DIM-01', 'Control of Documented Information', 'Active', '1', 60, [
            ['created draft', 60], ['submitted for review', 55], ['review passed', 50, 'Reviewer'],
            ['approved', 40, 'Approver'], ['assigned code', 39, 'Document Controller'],
        ], effectiveDaysAgo: 39, justification: 'Updated retention periods and added electronic records controls.');

        $this->procedure('BPL', 'SP-BPL-01', 'Business Planning and Strategy Review', 'Active', '0', 120, [
            ['created draft', 120], ['submitted for review', 118], ['review passed', 115, 'Reviewer'],
            ['approved', 112, 'Approver'], ['assigned code', 111, 'Document Controller'],
        ], effectiveDaysAgo: 111);

        // Waiting on Rina.
        $this->procedure('HRM', 'SP-HRM-01', 'Recruitment and Selection', 'For Review', '0', 6, [
            ['created draft', 6], ['submitted for review', 2],
        ]);

        // Waiting on Alex.
        $this->procedure('CIM', 'SP-CIM-01', 'Internal Audit', 'For Approval', '0', 14, [
            ['created draft', 14], ['submitted for review', 10], ['review passed', 3, 'Reviewer'],
        ]);

        // Waiting on Dana.
        $this->procedure('CIM', 'SP-CIM-02', 'Corrective Action', 'Pending Code', '0', 20, [
            ['created draft', 20], ['submitted for review', 18], ['review passed', 12, 'Reviewer'],
            ['approved', 1, 'Approver'],
        ], effectiveDaysAgo: 1);

        // Waiting on Paolo: fresh draft.
        $this->procedure('HRM', 'SP-HRM-02', 'Training and Competence', 'Draft', '0', 1, [
            ['created draft', 1],
        ]);

        // Waiting on Paolo: sent back by Rina with comments.
        $supplier = $this->procedure('PUR', 'SP-PUR-01', 'Supplier Evaluation and Selection', 'For Revision', '0', 9, [
            ['created draft', 9], ['submitted for review', 7], ['Sent back with Comments', 4, 'Reviewer'],
        ]);

        foreach ([
            'objectives' => 'Add a measurable objective, e.g. the minimum supplier score for accreditation.',
            'flowchart' => 'Step 3 should say who signs off the evaluation, not just "Purchasing".',
            'interfaces' => 'Link the Approved Supplier List as an output of step 4.',
        ] as $section => $comment) {
            $this->row(ProcedureComments::class, [
                'document_id' => $supplier->id,
                'user_id' => $this->people['Reviewer']->id,
                'section' => $section,
                'comment' => $comment,
                'stage' => 'review',
                'resolved' => false,
            ], $this->daysAgo(4));
        }
    }

    /**
     * @param  array<int, array{0: string, 1: int, 2?: string}>  $trail  [action, days ago, actor role (default Process Owner)]
     */
    private function procedure(
        string $section,
        string $code,
        string $title,
        string $status,
        string $revision,
        int $createdDaysAgo,
        array $trail,
        ?int $effectiveDaysAgo = null,
        ?string $justification = null,
    ): Document {
        $content = self::PROCEDURE_CONTENT[$title];

        $doc = $this->row(Document::class, [
            'type' => 'System Procedures',
            'title' => $title,
            'code' => $code,
            'section_id' => $this->sections[$section]->id,
            'revision_number' => $revision,
            'effective_date' => $effectiveDaysAgo !== null ? $this->daysAgo($effectiveDaysAgo)->toDateString() : null,
            'objective' => Str::markdown($content['objective']),
            'scope' => Str::markdown($content['scope']),
            'justification' => $justification ?? $content['justification'],
            'status' => $status,
        ], $this->daysAgo($createdDaysAgo));

        foreach ($content['steps'] as [$responsibility, $activity, $inputs, $outputs]) {
            $step = $this->row(ProcedureSteps::class, [
                'document_id' => $doc->id,
                'responsibility' => $responsibility,
                'activities' => $activity,
            ], $this->daysAgo($createdDaysAgo));

            foreach (['input' => $inputs, 'output' => $outputs] as $type => $titles) {
                foreach ($titles as $interface) {
                    $this->row(StepDocuments::class, [
                        'procedure_step_id' => $step->id,
                        'type' => $type,
                        'category' => 'Document',
                        'title' => $interface,
                    ], $this->daysAgo($createdDaysAgo));
                }
            }
        }

        foreach ($trail as $entry) {
            [$action, $daysAgo] = $entry;
            $this->log($doc, $action, $entry[2] ?? 'User', $this->daysAgo($daysAgo));
        }

        return $doc;
    }

    private function log(Document $doc, string $action, string $actorRole, CarbonInterface $at): void
    {
        [$description, $from, $to] = match ($action) {
            'created draft' => ['System Procedure document draft has been created.', null, null],
            'submitted for review' => ["System Procedure document ({$doc->code}) has been submitted for review.", null, null],
            'review passed' => ["System Procedure document ({$doc->code}) passed review and was sent for approval.", null, null],
            'approved' => ["System Procedure document ({$doc->code}) has been approved.", null, null],
            'assigned code' => ["Assigned code: {$doc->code}; Revision: {$doc->revision_number}", 'Pending Code', 'Active'],
            'superseded' => ["System Procedure document ({$doc->code}) revision {$doc->revision_number} has been superseded by revision ".($doc->revision_number + 1), 'Active', 'Superseded'],
            'Sent back with Comments' => ["System Procedure document ({$doc->code}) has been sent back by Reviewer with comments to be revised.", null, null],
        };

        $this->row(ActivityLog::class, [
            'action' => $action,
            'description' => $description,
            'status_from' => $from,
            'status_to' => $to,
            'document_id' => $doc->id,
            'document_type' => 'system_procedure',
            'user_id' => $this->people[$actorRole]->id,
            'acting_as_consultant' => false,
            'home_company_id' => $this->company->id,
            'performed_at' => $at,
        ], $at);
    }

    /**
     * Create a tenant row stamped with this company and back-dated timestamps.
     *
     * @template T of Model
     *
     * @param  class-string<T>  $model
     * @return T
     */
    private function row(string $model, array $attributes, CarbonInterface $at): Model
    {
        $instance = new $model;
        $instance->forceFill($attributes + ['created_at' => $at, 'updated_at' => $at]);

        $table = $instance->getTable();
        $this->tenantTables[$table] ??= $instance->getConnection()->getSchemaBuilder()->hasColumn($table, 'company_id');

        if ($this->tenantTables[$table]) {
            $instance->company_id = $this->company->id;
        }

        $instance->save();

        return $instance;
    }

    private function daysAgo(int $days): CarbonInterface
    {
        return $this->opensAt->copy()->subDays($days)->setTime(9, 0)->addMinutes(($days * 37) % 420);
    }

    /**
     * Sample procedure content: objective, scope, default justification, and
     * steps as [responsibility, activity, input interfaces, output interfaces].
     */
    private const PROCEDURE_CONTENT = [
        'Control of Documented Information' => [
            'objective' => "- To ensure documented information is approved, current and available where needed.\n- To prevent the unintended use of obsolete documents.",
            'scope' => '- Covers all controlled procedures, manuals, forms and records of the organization.',
            'justification' => 'Initial issue of the document control procedure.',
            'steps' => [
                ['Process Owner', 'Draft or revise the document using the approved template', ['Document Change Request'], ['Draft Document']],
                ['Reviewer', 'Review the draft for adequacy and consistency with related documents', ['Draft Document'], ['Review Comments']],
                ['Approver', 'Approve the document for release', ['Reviewed Document'], ['Approved Document']],
                ['Document Controller', 'Assign the document code and revision, and distribute controlled copies', ['Approved Document'], ['Master List of Documents', 'Distribution Record']],
                ['Document Controller', 'Retrieve and mark superseded copies as obsolete', ['Distribution Record'], ['Obsolete Document Log']],
            ],
        ],
        'Business Planning and Strategy Review' => [
            'objective' => '- To set annual business objectives aligned with the quality policy and strategic direction.',
            'scope' => '- Applies to the annual planning cycle and the mid-year strategy review.',
            'justification' => 'Formalize the annual planning cycle ahead of certification.',
            'steps' => [
                ['Top Management', 'Review internal and external issues and interested-party needs', ['SWOT Analysis', 'Customer Feedback Summary'], ['Context Review Minutes']],
                ['Process Owners', 'Propose departmental objectives and targets', ['Context Review Minutes'], ['Draft Objectives']],
                ['Top Management', 'Approve the business plan and quality objectives', ['Draft Objectives'], ['Approved Business Plan']],
                ['Process Owners', 'Monitor objectives monthly and report progress', ['Approved Business Plan'], ['Objectives Monitoring Report']],
            ],
        ],
        'Recruitment and Selection' => [
            'objective' => "- To hire competent personnel through a fair and documented selection process.\n- To fill approved vacancies within 30 working days.",
            'scope' => '- Covers all regular, probationary and project-based positions.',
            'justification' => 'New procedure requested after the last management review.',
            'steps' => [
                ['Department Head', 'Submit a manpower request with the job description', ['Approved Organizational Chart'], ['Manpower Request Form']],
                ['HR Officer', 'Source and screen applicants against the job qualifications', ['Manpower Request Form'], ['Shortlist of Applicants']],
                ['Department Head, HR Officer', 'Conduct interviews and skills assessments', ['Shortlist of Applicants'], ['Interview Evaluation Form']],
                ['Operations Manager', 'Approve the hiring recommendation', ['Interview Evaluation Form'], ['Job Offer']],
            ],
        ],
        'Internal Audit' => [
            'objective' => '- To verify that the management system conforms to requirements and is effectively implemented.',
            'scope' => '- Covers all processes within the management system, audited at least once a year.',
            'justification' => 'Required by ISO 9001:2015 clause 9.2.',
            'steps' => [
                ['QMS Head', 'Prepare the annual audit programme', ['Previous Audit Results'], ['Internal Audit Programme']],
                ['Lead Auditor', 'Prepare the audit plan and checklist', ['Internal Audit Programme'], ['Audit Plan', 'Audit Checklist']],
                ['Audit Team', 'Conduct the audit and record findings', ['Audit Checklist'], ['Audit Findings']],
                ['Lead Auditor', 'Issue the audit report and raise corrective action requests', ['Audit Findings'], ['Internal Audit Report', 'Corrective Action Request']],
            ],
        ],
        'Corrective Action' => [
            'objective' => '- To eliminate the root causes of nonconformities and prevent recurrence.',
            'scope' => '- Applies to nonconformities from audits, customer complaints and process failures.',
            'justification' => 'Separate corrective action from the internal audit procedure.',
            'steps' => [
                ['Process Owner', 'Contain the nonconformity and record it', ['Corrective Action Request'], ['Nonconformity Report']],
                ['Process Owner', 'Determine root cause using 5-Why or fishbone analysis', ['Nonconformity Report'], ['Root Cause Analysis']],
                ['Process Owner', 'Implement corrective actions', ['Root Cause Analysis'], ['Action Plan']],
                ['QMS Head', 'Verify effectiveness and close the request', ['Action Plan'], ['Closed Corrective Action Request']],
            ],
        ],
        'Training and Competence' => [
            'objective' => '- To ensure personnel are competent on the basis of education, training and experience.',
            'scope' => '- Covers onboarding, job-specific and refresher training for all employees.',
            'justification' => 'Training records are currently kept per department with no common process.',
            'steps' => [
                ['Department Head', 'Identify competence gaps per position', ['Competency Matrix'], ['Training Needs Analysis']],
                ['HR Officer', 'Prepare and schedule the training plan', ['Training Needs Analysis'], ['Annual Training Plan']],
                ['HR Officer', 'Conduct training and evaluate effectiveness', ['Annual Training Plan'], ['Training Attendance Sheet', 'Training Evaluation Form']],
            ],
        ],
        'Supplier Evaluation and Selection' => [
            'objective' => '- To select and retain suppliers capable of meeting requirements.',
            'scope' => '- Covers suppliers of raw materials, components and outsourced services.',
            'justification' => 'Customer audit found no documented supplier evaluation.',
            'steps' => [
                ['Purchasing Officer', 'Identify prospective suppliers and request company profiles', ['Purchase Requisition'], ['Supplier Profile']],
                ['Purchasing Officer', 'Evaluate suppliers on quality, delivery and price', ['Supplier Profile'], ['Supplier Evaluation Form']],
                ['Purchasing', 'Approve accredited suppliers', ['Supplier Evaluation Form'], []],
                ['Purchasing Officer', 'Re-evaluate suppliers annually', ['Supplier Performance Records'], ['Supplier Rating Report']],
            ],
        ],
    ];
}
