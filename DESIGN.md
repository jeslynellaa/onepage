---
name: OnePage
description: ISO-style controlled document management for FCU Solutions Inc. and its consulting clients
colors:
  action-blue: "#2563eb"
  action-blue-hover: "#1d4ed8"
  action-blue-soft: "#eff6ff"
  onepage-mint: "#3de3b1"
  onepage-mint-deep: "#2db68e"
  onepage-indigo: "#575df9"
  success-green: "#16a34a"
  success-green-soft: "#dcfce7"
  success-green-text: "#166534"
  danger-red: "#dc2626"
  danger-red-soft: "#fee2e2"
  danger-red-text: "#991b1b"
  warning-amber-soft: "#fef3c7"
  warning-amber-border: "#fcd34d"
  warning-amber-text: "#78350f"
  neutral-canvas: "#f3f4f6"
  neutral-surface: "#ffffff"
  neutral-border: "#e5e7eb"
  neutral-border-soft: "#f3f4f6"
  neutral-text-primary: "#111827"
  neutral-text-secondary: "#4b5563"
  neutral-text-muted: "#9ca3af"
typography:
  display:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "normal"
  headline:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: "normal"
  body:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "Outfit, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "0.05em"
rounded:
  sm: "6px"
  md: "8px"
  lg: "12px"
  xl: "16px"
  2xl: "24px"
  full: "9999px"
components:
  button-primary:
    backgroundColor: "{colors.action-blue}"
    textColor: "#ffffff"
    rounded: "{rounded.md}"
    padding: "10px 16px"
  button-primary-hover:
    backgroundColor: "{colors.action-blue-hover}"
  badge-active:
    backgroundColor: "{colors.onepage-mint}"
    textColor: "{colors.onepage-mint-deep}"
    rounded: "{rounded.full}"
    padding: "2px 8px"
  badge-workflow:
    backgroundColor: "{colors.onepage-indigo}"
    textColor: "{colors.onepage-indigo}"
    rounded: "{rounded.full}"
    padding: "2px 8px"
  badge-neutral:
    backgroundColor: "{colors.neutral-canvas}"
    textColor: "{colors.neutral-text-muted}"
    rounded: "{rounded.full}"
    padding: "2px 8px"
  card:
    backgroundColor: "{colors.neutral-surface}"
    rounded: "{rounded.2xl}"
    padding: "20px"
---

# Design System: OnePage

## Overview

**Creative North Star: "The Audit Trail"**

OnePage is documentation-first software: the record is the hero, and the interface around it earns its place only where it aids comprehension. Screens are built for a consultant or document controller moving fast through a Draft → Review → Approval → Coding → Active lifecycle across many documents and, often, many client tenants at once — density, scanability, and unambiguous status are the design's job, not decoration for its own sake.

Against that procedural backbone sits one deliberate flourish: the sidebar's mint-to-indigo gradient and the two brand accent colors it introduces (OnePage Mint, OnePage Indigo) are the product's signature, and they are load-bearing — they double as the system's only status-color vocabulary. Everywhere else, the palette stays quiet: neutral grays, flat white cards, shallow shadows. The interface does not compete with the documents it manages.

**Key Characteristics:**
- Gray-neutral canvas (`bg-gray-100`) with white, low-elevation cards — flat-with-soft-lift is the intended resting state, not a placeholder.
- Two-color brand system (OnePage Mint, OnePage Indigo) reserved for identity (sidebar) and document-status signaling — never used as generic decoration.
- A separate, plain Action Blue (`blue-600`) drives every interactive control (buttons, links, focus rings) — distinct from the brand pair.
- Small, dense type (`text-xs`/`text-sm` dominate) and bold, uppercase micro-labels for status and section headers — built for scanning tables of documents, not for display reading.
- Pill-shaped status badges and heavily rounded containers (`rounded-2xl`/`3xl`) soften an otherwise utilitarian, table-dense UI.

## Colors

Two systems run in parallel: a neutral operating palette that does almost all the work, and a two-color brand pair reserved for identity and status.

### Primary
- **Action Blue** (`#2563eb`, hover `#1d4ed8`): every interactive control — primary buttons, links, focus states, active table actions. This is the color a user clicks, never the color a user reads as system status.

### Secondary
- **OnePage Mint** (`#3de3b1`, deep variant `#2db68e`): sidebar gradient start; the exclusive color for "Active" document status (`bg-[#3de3b1]/10 text-[#2db68e]`, a soft tint with a deepened text tone).
- **OnePage Indigo** (`#575df9`): sidebar gradient end, the active-nav-item text tint, and the exclusive color for any in-motion workflow status ("For Review," "For Approval") via the same soft-tint pattern (`bg-[#575df9]/10 text-[#575df9]`).

### Neutral
- **Canvas** (`#f3f4f6`, gray-100): the page background behind every authenticated view.
- **Surface** (`#ffffff`): cards, panels, table containers, modals.
- **Border** (`#e5e7eb` gray-200 / `#f3f4f6` gray-100): card and table hairlines; gray-200/300 for form-field borders.
- **Text Primary** (`#111827` gray-900): headings, key data values.
- **Text Secondary** (`#4b5563`/`#374151`, gray-600/700): body copy, table cell text.
- **Text Muted** (`#9ca3af`, gray-400): placeholders, empty-state copy, de-emphasized metadata (page counts, timestamps).

### Semantic
- **Success Green** (`#16a34a` on `#dcfce7`, text `#166534`): flash-message success banners only.
- **Danger Red** (`#dc2626` on `#fee2e2`, text `#991b1b`): flash-message errors, validation messages, destructive actions.
- **Warning Amber** (`#fef3c7` background, `#fcd34d` border, `#78350f` text): the one system-level banner reserved for consultant mode ("Working in: [Client]") — its rarity is what makes it legible as a mode indicator rather than an alert.

### Named Rules
**The Two-Blue Rule.** Action Blue and OnePage Indigo are never interchangeable. Action Blue means "you can click this." OnePage Indigo means "this document is in motion." A button never uses Indigo; a status badge never uses Action Blue.

**The Brand-Color-Is-Status Rule.** OnePage Mint and OnePage Indigo appear in exactly two places: the sidebar chrome and document-status badges. Introducing them anywhere else (a random highlight, a decorative accent) breaks the signal they carry.

## Typography

**Display/Body/Label Font:** Outfit (variable weight 100–900), falling back to `ui-sans-serif, system-ui, sans-serif`. The entire product — from the landing page to the densest data table — runs on this single family; there is no secondary or monospace face, except an incidental `font-mono` on document codes to visually distinguish them as identifiers.

**Character:** A single geometric sans carrying the whole system means hierarchy is built almost entirely through weight and size, not typeface contrast — bold and uppercase do the work italics or a second family would do elsewhere.

### Hierarchy
- **Display** (700, `text-3xl`/`text-2xl`, tight leading): page-level headings ("Documents," "Settings").
- **Headline** (600–700, `text-xl`/`text-lg`): section and card headers, modal titles.
- **Body** (400–500, `text-sm`): table cell content, form labels, paragraph copy.
- **Label** (700, `text-xs`, `tracking-wider`, uppercase): status badges, primary-button text, section eyebrows. This is the system's most-used type role by volume.

### Named Rules
**The Uppercase-Label Rule.** Anything that functions as a system label rather than prose — button text, status badges, table-column eyebrows — is `text-xs`, bold, and uppercase with wide tracking. Body and heading text is never uppercased.

## Layout

Fixed-sidebar app shell: a collapsible left sidebar (`w-16` collapsed / `w-56` expanded, Alpine-driven) with a fixed top header, and a scrolling main content area offset by the sidebar's current width. Page content sits on the gray-100 canvas inside `max-w-4xl`-constrained banners (flash messages) or full-width white card containers for tables and forms.

Density is high by default — this is Operate-mode software for reviewing many documents, not a spacious marketing layout. Tables and list rows use tight vertical padding (`py-2`–`py-4`) and small type; forms use standard Tailwind form-field spacing (`px-3 py-2`) with visible borders even at rest (`border-gray-300`), not the low-contrast borderless style some SaaS forms use — every field must be identifiable as a field before focus.

The landing page (`landing-layout.blade.php`) is the one Persuade-mode surface and departs from this density model with generous section padding; it is out of scope for this operating system's rhythm.

## Elevation & Depth

Flat-with-soft-lift, deliberately. Surfaces are mostly flat (`shadow-sm`, sometimes none) with a thin neutral border doing more of the separation work than shadow does. Shadow escalates only for genuinely overlaid content — modals use `shadow-xl`, dropdown tooltips use `shadow-lg` — never for routine cards or buttons, where `shadow-sm` or no shadow is standard.

### Shadow Vocabulary
- **Resting card** (`box-shadow` per Tailwind `shadow-sm`): default card/table/panel elevation.
- **Interactive lift** (`shadow-md`): primary buttons and sidebar chrome, signaling "this is the actionable layer."
- **Overlay** (`shadow-xl`/`shadow-2xl`): modals and dialogs only.

### Named Rules
**The Border-Before-Shadow Rule.** Reach for a `border-gray-100`/`200` hairline to separate a surface from its background before reaching for a heavier shadow. Shadow is reserved for things that are actually floating above the page (modals, tooltips), not for routine cards.

## Shapes

Rounding is generous and increases with a container's visual importance: small interactive elements (inputs, inline buttons) use `rounded-md`/`lg` (6–8px); cards and panels commonly jump straight to `rounded-2xl`/`3xl` (16–24px); status badges and pill buttons use `rounded-full`. There is no sharp-cornered (`rounded-none`) surface in the authenticated app outside of a few incidental table cells — hard corners read as an exception, not the rule.

## Components

### Buttons
- **Shape:** `rounded-md`/`lg` (6–8px) for most actions; `rounded-full` for a small number of pill-style CTAs (landing page, some modals).
- **Primary:** Action Blue fill (`bg-blue-600`), white text, bold uppercase `text-xs` label, `shadow-sm`, generous horizontal padding (`px-4`–`px-8`).
- **Hover / Active:** background deepens one step (`hover:bg-blue-700`); CTA-style buttons additionally get a subtle press scale (`active:scale-95`).
- **Ghost / Dashed (secondary):** transparent or tinted background matching the current context color (e.g. `bg-blue-50/50` with `border-dashed border-blue-200` for an "add" affordance), text in that same hue — used for low-emphasis, repeatable actions like "add interface."

### Status Badges (signature component)
- **Shape:** `rounded-full`, `px-2 py-0.5`, `text-[9pt]` bold uppercase.
- **Active:** `bg-[#3de3b1]/10` on `#2db68e` text (OnePage Mint).
- **In workflow** (Review/Approval): `bg-[#575df9]/10` on `#575df9` text (OnePage Indigo).
- **Everything else** (Draft, For Revision, Not Approved, Pending Code, Superseded, Archived): falls back to plain `bg-gray-100 text-gray-500` — the system currently only gives dedicated color to "good" (Active) and "in motion" (Review/Approval) states; every other status reads as neutral gray.

### Cards / Containers
- **Corner Style:** `rounded-2xl`/`3xl`.
- **Background:** white on the gray-100 canvas.
- **Shadow Strategy:** `shadow-sm`, paired with a `border-gray-100` hairline (see Elevation & Depth).
- **Internal Padding:** `p-5`/`p-6` typical.

### Inputs / Fields
- **Style:** visible border at rest (`border-gray-300`, `rounded-lg`), white background — fields never rely on focus alone to become identifiable.
- **Focus:** border and ring both shift to sky (`ring-2 ring-sky-500 border-sky-500`), a global rule applied to every `input`/`select`/`textarea`.

### Navigation (sidebar)
- **Style:** fixed, collapsible (`w-16` ↔ `w-56`), mint-to-indigo diagonal gradient (`bg-gradient-to-tr from-[#3de3b1] to-[#575df9]`), white text and icons.
- **Active item:** a soft white-tinted gradient pill (`from-white/15 to-white/60`) with dark navy text (`#001f3f`) — the one place a nav item's text goes dark instead of white.
- **Default/Hover:** white text; hover adds a white border outline rather than a fill change.
- **Collapsed state:** icon-only with a dark tooltip (`bg-gray-800`) on hover.

## Do's and Don'ts

### Do:
- **Do** keep Action Blue exclusive to interactive controls and OnePage Mint/Indigo exclusive to brand chrome and status badges (see the Two-Blue Rule).
- **Do** pair every card/panel with a hairline border before adding shadow weight.
- **Do** use bold uppercase `text-xs` for anything functioning as a label, badge, or button — never for prose.
- **Do** keep form fields visibly bordered at rest; focus is an enhancement, not the only affordance.

### Don't:
- **Don't** introduce a third accent color into the status-badge system without extending the badge vocabulary deliberately — today it is binary (Active / In-workflow) plus a neutral fallback, not five distinct hues.
- **Don't** use OnePage Mint or Indigo as decorative highlight color outside the sidebar and status badges.
- **Don't** reach for heavy shadow (`shadow-lg`+) on routine, non-overlaid surfaces — that weight is reserved for modals and floating elements.
