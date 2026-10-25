# Implementation Plan and Test Strategy

## Phase 0 --- Repository and environment discovery

-   Inspect Laravel/PHP versions, existing code, dependencies, database,
    auth, Tailwind build pipeline, tests, and cPanel constraints.
-   Identify whether this is a new project or an existing application.
-   Do not overwrite existing work without understanding it.
-   Record setup/deployment assumptions.

## Phase 1 --- Foundation and design system

-   Configure authentication and password reset.
-   Build app/guest layouts and reusable Blade components.
-   Implement responsive sidebar/topbar and Neo-Brutalism tokens.
-   Add navigation only for implemented routes.
-   Verify desktop/mobile behavior and accessibility.

## Phase 2 --- Core masters and cash transactions

-   Parties (client/vendor/both, one primary PIC).
-   Projects, categories, actual cash transactions, optional receipt
    uploads.
-   Validation, filters, pagination, audit events.
-   Seed default categories without duplicating them on repeated
    seeding.

## Phase 3 --- Bills and payments

-   Receivables and payables.
-   Due dates, overdue indicator, partial/full payment.
-   Atomic payment posting to linked cash transaction.
-   Prevent overpayment and duplicate payment submissions.
-   Test all cash and outstanding-balance calculations.

## Phase 4 --- Director advances

-   Record company expense paid personally.
-   Track liability outstanding and partial/full reimbursement.
-   Ensure reimbursement reduces cash/liability without double-counting
    operating expense.

## Phase 5 --- Dashboard, reports, export, reconciliation

-   Current-month summary and Jan--Dec annual chart.
-   Category/project/cashflow reports.
-   Filters shared with export.
-   CSV/Excel and PDF exports.
-   Manual reconciliation and discrepancy display.

## Phase 6 --- CSV import

-   Preview, row classification, mapping, duplicate detection, explicit
    review of ambiguous rows, import batch report, totals and balance
    reconciliation.
-   Test dry-run and rerun idempotency.
-   Test the provided CSV with special focus on date-without-year rows
    and the final bank-movement row.

## Phase 7 --- Hardening and deployment

-   Security review, authorization, upload privacy, audit review.
-   Automated and manual backups; restore test.
-   Production `.env` and HTTPS configuration.
-   Document cPanel deployment, cron/scheduler setup, storage
    permissions, database migration, and rollback.
-   Run all automated tests and a manual acceptance checklist.

## Required test cases

1.  Income increases cash; expense decreases cash.
2.  Unpaid receivable/payable does not change cash.
3.  Full receivable payment creates one linked income transaction.
4.  Partial receivable payment leaves correct outstanding balance.
5.  Full/partial payable payment creates correct expense cash movement.
6.  Duplicate payment request cannot double-post.
7.  Overpayment is rejected.
8.  Director-paid expense increases expense reporting/liability but not
    bank cash.
9.  Reimbursement reduces bank cash/liability but does not double-count
    expense.
10. Project-related transactions/documents require a project.
11. Category type mismatch is rejected.
12. Archived/corrected transactions retain audit history.
13. Reconciliation records difference without silently changing cash.
14. CSV summary/report rows are skipped.
15. Ambiguous dates and missing amounts are flagged.
16. Import rerun does not duplicate rows.
17. Export respects active filters.
18. Reset-password flow uses configured email transport.
19. Receipt upload rejects invalid file types and unauthorized download.
20. Dashboard monthly/annual figures are derived from database and match
    tested transactions.
