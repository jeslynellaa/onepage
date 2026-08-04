# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 12 multi-tenant SaaS app for managing controlled documents (ISO-style document control): System Procedures, MS Manuals, Support Documents, and Forms, each with a Draft → Review → Approval → Coding → Active/Superseded workflow, activity logging, and PDF generation. Backend is Blade + Livewire 3 + Tailwind v4 (via Vite), no SPA/API layer.

## Commands

```bash
# Install
composer install
npm install

# Local dev (serves app, queue listener, log tailer, and Vite together)
composer dev

# Individually
php artisan serve
npm run dev          # Vite dev server
npm run build         # Production asset build

# Tests (uses in-memory sqlite, see phpunit.xml)
composer test          # clears config cache then runs php artisan test
php artisan test
php artisan test --filter=TestName
php artisan test tests/Feature/SomeTest.php

# Code style
vendor/bin/pint         # Laravel Pint (PHP formatter)

# DB
php artisan migrate
php artisan migrate:fresh --seed
```

There is no JS/PHP linter beyond Pint configured in this repo, and no CI config present.

## Architecture

### Multi-tenancy (company scoping)

Every tenant is a `Company`. Most domain models (`Document`, `MsManual`, `SupportDocument`, `Section`, `ProcedureSteps`, `ActivityLog`, etc.) are scoped to the logged-in user's company via a global scope, `App\Models\Scopes\CompanyScope`, applied in each model's `booted()`:

```php
static::addGlobalScope(new CompanyScope);
static::creating(fn ($model) => $model->company_id = App\Support\CompanyContext::id());
```

`App\Support\CompanyContext::id()` returns the acting company for the current request: normally `auth()->user()->company_id`, but if a host-company consultant has "entered" a client's space (an `active_client_id` session key backed by a live, non-revoked `App\Models\ClientUser` row), it returns the client's company_id instead. When adding a new tenant-owned model, follow this same pattern (add the scope + auto-set `company_id` via `CompanyContext::id()` on create) rather than filtering by company_id manually in controllers. `company_id = 1` is treated as the platform/host company (see the `enter-admin` gate in `AppServiceProvider`, restricted to `role === 'Admin' && company_id === 1`).

Host-company ("FCU") consultants are assigned per-client via the `client_users` table (`App\Models\ClientUser`, with a `status`/`revoked_at` for soft revocation) rather than a raw pivot — see `Company::consultants()`/`User::clients()`. A consultant enters/exits a client's space via `ConsultantController` (`/consultant/clients`), which sets/clears the `active_client_id` session key; `App\Http\Middleware\ResolveClientContext` re-validates that key against `client_users` on every request so a revoked assignment loses access immediately. `ActivityLog` rows carry `acting_as_consultant`/`home_company_id` so consultant actions inside a client's space are distinguishable from the client's own staff.

### Document types and workflow

Four document types live in parallel, each with its own model, controller, and Blade view namespace, and its own status-transition logic (they are not currently unified behind a shared base class):

- System Procedures — `Document` model / `DocumentController` / `resources/views/document/system_procedures`
- MS Manual — `MsManual` model / `MsManualController` / `resources/views/document/ms_manual`
- Support Documents — `SupportDocument` model / `SupportDocumentController` / `resources/views/document/support_documents`
- Forms — `Form` model / `FormsManualController` / `resources/views/document/forms_manual`

Common status flow (string `status` column, not an enum): `Draft` → `For Review` → `For Revision` (if rejected) → `For Approval` → `Not Approved` or `Pending Code` → `Active`. Documents being replaced move to `Superseded`; soft-deleted documents get `Archived` plus `delete_justification`.

Each `Section` (a process/category within a company) has a `process_owner_id`, `reviewer_id`, and `approver_id` (all `User` FKs). Authorization for workflow transitions is driven by comparing the current user's id/role against these section roles and the document's current status — see `App\Policies\DocumentPolicy` as the canonical pattern (`sendForReview`, `review`, `approve`, `setCode`, `viewRevisionHistory`) and replicate it for the other document-type policies.

`ProcedureComments` stores reviewer/approver feedback per document, tagged with a `stage`.

### Document steps (Livewire)

System Procedure documents have ordered `ProcedureSteps`, each of which can have `StepDocuments` (input/output interfaces). Step editing on the create/edit document forms is handled by the `App\Livewire\ProcedureSteps` component (`resources/views/livewire/`).

### PDF generation

`App\Services\DocumentPdfService::generate()` renders `resources/views/pdf/system_procedure.blade.php` via `barryvdh/laravel-dompdf`, embedding the company logo, hex/brand color (auto contrast text via `getTextColorForBackground`), font, and paper size (all company-configurable), plus process owner/reviewer/approver signature images. It also writes the resulting page count back onto the document (`pages` column) and injects page numbers via a Dompdf canvas page-script. Note the local-vs-production branching for resolving storage file paths (`public_path('storage/...')` locally vs `Storage::disk('public')->path(...)` in production) — preserve this when touching signature/logo path resolution.

### Activity logging

`ActivityLog` is a single polymorphic-ish table keyed by `document_id` + `document_type` (`system_procedure` / `ms_manual` / `support_document`), with per-type relations (`document()`, `ms_manual()`, `support_document()`) that filter on `document_type`. When adding logging for a new action, write to this table rather than adding a per-type log table.

### Auth & roles

Custom session auth via `AuthController` (not Laravel Breeze/Fortify) — `role` is a free-text string on `User` (e.g. `Admin`, `Document Controller`), not a spatie/permission package. Role checks are done inline in controllers/policies (`$user->role === 'Document Controller'`). All authenticated routes go through the `auth` + `nocache` (custom `App\Http\Middleware\NoCache`, sets no-store headers) middleware group in `routes/web.php`. Client (tenant) onboarding and invitations are handled by `ClientController` + `Invitation` model + `InvitationMail`.

### Route/controller conventions

All app routes are defined directly in `routes/web.php` (no route-model-binding route groups per resource; instead there's one flat `auth`+`nocache` group). Route names follow `document.<type>.<action>` (e.g. `document.system_procedures.forReview`, `document.ms_manual.approveOrNot`). When adding a document-type action, mirror the naming and URL shape already used for System Procedures.

### Database

Default connection is SQLite (`database/database.sqlite`) even outside tests. Company-scoped tables all carry a `company_id` FK added via later migrations (added after initial table creation — check migration history rather than assuming `company_id` is in the original `create_*_table` migration for a given model).
