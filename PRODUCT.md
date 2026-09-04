# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primary design audience: **FCU consultants** — staff of FCU Solutions Inc. (the host company, `company_id = 1`) who are assigned to client companies via `client_users`, "enter" a client's tenant space to work inside it, and administer/onboard clients (`ConsultantController`, `active_client_id` session context). Their job is running ISO/management-systems consulting engagements through the product: setting up a client's sections and roles, guiding documents through Draft → Review → Approval → Coding → Active, and monitoring compliance status across one or many client tenants.

Secondary users: client-company staff who hold workflow roles on a `Section` — process owner, reviewer, approver — plus roles such as `Admin`, `Document Controller`, and `Top Management`, who use the product day to day without a consultant present.

## Product Purpose

OnePage centralizes an organization's controlled documents (System Procedures, MS Manuals, Support Documents, Forms) and manages them through an ISO-style document-control lifecycle — Draft, Review, Approval, Coding, Active/Superseded/Archived — with per-step authorization, reviewer/approver feedback, activity logging, and branded PDF output. Success means a client company reaches and stays in ISO audit-readiness with less manual document wrangling, and FCU consultants can run that process for multiple clients from one tool instead of ad hoc spreadsheets/files.

## Positioning

OnePage is not generic document-management software — it is the delivery vehicle for FCU Solutions Inc.'s own ISO/QMS consulting methodology. The document lifecycle, role structure (process owner/reviewer/approver per section), and activity trail encode how FCU actually runs a compliance engagement. The consultant-entry mechanism (`ClientUser` assignment, entering/exiting a client's space, `acting_as_consultant` activity attribution) is the concrete, uncopyable feature: a consultant works *inside* the client's real tenant, not a shared admin panel or an export/import workflow. A neighboring product could sell document storage or workflow approval; it could not truthfully claim FCU's methodology or this consultant-in-tenant mechanism.

## Operating Context

- Multi-tenant SaaS; every client organization is a `Company`. FCU itself is the host company/tenant.
- Four parallel controlled-document types (System Procedures, MS Manual, Support Documents, Forms), each with its own status lifecycle, policy, and Blade namespace.
- Workflow is section-scoped: each `Section` (a process/category within a company) has a designated process owner, reviewer, and approver; transitions are authorized by comparing the acting user against those roles plus the document's current status.
- Consultants assigned to a client can enter that client's tenant space to act on its behalf; a revoked assignment loses access immediately on the next request.
- Every document action is written to a shared `ActivityLog`, distinguishing consultant actions (acting on behalf of a client) from the client's own staff actions.
- Final documents are generated as PDFs, each carrying the *client* company's own logo, brand color, font, and paper size (not FCU's) — the app's own identity and each client's document identity are deliberately separate.

## Capabilities and Constraints

- Backend is server-rendered Blade + Livewire 3 + Tailwind v4 via Vite — no SPA/API layer, no native or mobile app.
- Auth/roles are a custom session system with a free-text `role` string on `User` (no permissions package); role checks are inline, not policy-table-driven beyond the per-document policies.
- Company-scoped data is enforced by a global Eloquent scope keyed off the acting company (client-context-aware when a consultant has entered a client's space), not manual per-controller filtering.
- Per-tenant PDF branding (logo, hex color with auto-contrast text, font, paper size) is a real, load-bearing configuration surface, not a nice-to-have — design work touching document output or company settings must account for it.
- Terminology to keep consistent: "Section" (a process/category with owner/reviewer/approver), "Coding" (assigning a document's controlled code before it goes Active), "Superseded"/"Archived" as distinct end states from "Active."

## Brand Commitments

- Product name is **OnePage**, developed by **FCU Solutions Inc.**, a management-systems/organizational-development consultancy (fcusolutions.org). This parent relationship is confirmed and should be preserved, not genericized.
- No customer testimonials, case studies, or client logos are on hand — none should be fabricated or implied. The existing landing page's "About" section (FCU Solutions Inc. background) is the only confirmed evidence-backed brand copy today.
- Per-client brand assets (logo, color, font) are user-provided at the tenant level and are out of scope for OnePage's own brand identity work.

## Evidence on Hand

- Landing page copy (`resources/views/landing/index.blade.php`) confirms current positioning language: "Centralized document control with version management, approval workflows, and automated distribution," "Monitor regulatory requirements and ISO standards," and the FCU Solutions Inc. "About" section.
- FCU logo asset exists at `public/img/fcu-logo.jpg`.
- No pricing, licensing, testimonials, or usage-metric claims are confirmed; do not invent any.

## Product Principles

1. Design for the consultant running the show, not just the end client — the primary user is often managing several tenants at once, not one.
2. Every workflow screen should make the section's role structure (owner/reviewer/approver) and the document's current lifecycle stage legible at a glance — this *is* the product's compliance value.
3. Keep OnePage's own brand identity and each client's document-output brand identity visually and conceptually separate; never let one leak into the other's design system.
4. Consultant actions inside a client space must stay visibly distinguishable from the client's own staff actions, in the UI and not just the activity log.
5. Favor operational clarity and auditability over decorative polish — this is Operate-mode software for a compliance workflow, not a marketing surface (the landing page is the one exception, as Persuade mode).
