# INTRASOL Cash Management --- Agent Rules

## Mission

Build a small internal cash-management web application for PT Integrasi
Solusi Teknologi (INTRASOL). The application records realized cash
movements, receivables/payables, director advances, simple project
attribution, reconciliation, reports, and safe CSV migration.

## Mandatory workflow

1.  Inspect the existing repository, Laravel version, PHP version,
    installed packages, database configuration, routes, tests, and
    deployment constraints before changing anything.
2.  Summarize the current architecture and propose a short
    implementation plan. Preserve working conventions unless a change is
    necessary.
3.  Read all relevant files under `instructions/` before implementing a
    feature.
4.  Implement in small, testable stages. Run available tests and
    relevant checks after each stage.
5.  Do not fabricate existing business data or silently infer ambiguous
    imported values.
6.  Do not claim a feature works unless it was implemented and verified.

## Product boundaries

-   This is a cash-management system, not a full accounting/ERP
    platform.
-   Do not add payroll, tax accounting, bank API integration,
    multi-currency, approval workflows, or complex project management.
-   Currency is IDR only.
-   One cash account is in scope: the Bank Jago personal account
    currently used exclusively for company funds. Represent it as the
    company's cash account without implying that the bank account is
    legally held by the company.
-   One administrator user is in scope. Do not build roles/teams unless
    explicitly requested.
-   No transaction-planning feature. Only actual cash transactions and
    receivable/payable documents.
-   Projects are simple labels/master records used for transaction
    reporting.

## Technical constraints

-   Laravel, Blade, Tailwind CSS, JavaScript as needed, MySQL.
-   Do not use Filament or another admin-panel framework.
-   Build reusable custom Blade layouts/components, including the admin
    sidebar and topbar.
-   Use Laravel conventions: migrations, Eloquent, Form Requests where
    appropriate, policies/middleware, CSRF protection, validation, and
    automated tests.
-   Use `Asia/Jakarta` timezone and IDR formatting.
-   Target shared hosting/cPanel. Avoid requirements for long-running
    workers or infrastructure that shared hosting may not provide. Use
    Laravel Scheduler via cron if available; document setup.

## Financial integrity --- non-negotiable

-   Only realized cash movements affect actual cash balance.
-   An unpaid receivable/payable must not affect actual cash balance.
-   Payment of a receivable/payable creates a linked cash transaction
    atomically; never create a second transaction for the same payment.
-   Partial payments must be supported; outstanding amount = original
    amount minus successful payments, never below zero.
-   Director-paid company expenses are company expenses and create a
    liability owed to the director, but do not reduce the Bank Jago cash
    balance. Reimbursement reduces cash and the director liability; it
    must not create a second expense.
-   Corrections and archival must preserve audit history. Do not
    hard-delete financial records through ordinary UI.
-   Reconciliation differences must not be silently categorized as
    normal income/expense.
-   Use database transactions for multi-record financial operations and
    protect against duplicate requests/idempotency where appropriate.

## CSV migration

-   Build an import preview/validation workflow or safe Artisan command
    plus an import report.
-   Ignore spreadsheet presentation rows, summary totals, and report
    widgets; import only verified transaction rows.
-   Preserve original source values for traceability.
-   Ambiguous dates (e.g. `14 Juli`, `7 September` without year),
    missing amounts, malformed rows, and uncertain categories must be
    flagged for human review. Never guess silently.
-   Detect likely duplicates and report them. Do not silently import
    duplicates.
-   Validate imported totals and running balances against the source.
    Report discrepancies; do not create artificial transactions to force
    a match.
-   Make imports safe to rerun using a source-row fingerprint/import
    batch identity.
-   Never treat a displayed summary total as a transaction.

## UI/UX

-   Follow `instructions/03-design-system.md`.
-   Avoid generic AI-looking gradients, excessive rounded cards,
    excessive whitespace, decorative charts without decision value, and
    inconsistent components.
-   Prefer clear typography, strong black borders, hard shadows, warm
    off-white surfaces, yellow accents, and purposeful green/red status
    colors.
-   The UI must be responsive and usable on desktop and mobile,
    especially the transaction form.
-   Keep data entry efficient despite the requested full form: group
    fields clearly and show conditional fields only when relevant.

## Completion criteria

A feature is complete only when validation, authorization,
empty/error/loading states where applicable, audit implications,
responsive behavior, and relevant tests are considered. Update
documentation when behavior or setup changes.
