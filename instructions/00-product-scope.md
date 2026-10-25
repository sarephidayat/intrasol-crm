# Product Scope and Requirements

## Product

Internal cash-management application for PT Integrasi Solusi Teknologi
(INTRASOL).

## Goals

1.  Make recording cash movements much easier than maintaining a
    spreadsheet.
2.  Show actual cash balance and monthly/yearly cash movement clearly.
3.  Track receivables and payables, including due dates, overdue status,
    and partial payments.
4.  Track director advances separately.
5.  Preserve transaction history and make CSV migration reviewable and
    auditable.

## In scope

-   Single administrator login using email and password; password reset
    by email.
-   Dashboard with current cash balance, current-month
    inflows/outflows/net movement, due/overdue bill alerts, and annual
    Jan--Dec income/expense chart.
-   Actual cash transactions: income and expenses.
-   Categories for income/expense.
-   Receivable and payable documents with party, issue date, due date,
    amount, project when applicable, notes, and payment history.
-   Partial and full payment of receivables/payables.
-   One reusable business-party master data model: a party may have role
    Client, Vendor, or Both; each party has one primary PIC/contact.
-   Simple project master list; no task management. A
    transaction/document related to a project must reference a project.
-   Director advances: record company expense paid personally,
    outstanding amount owed to director, and reimbursement.
-   Manual reconciliation against actual Bank Jago balance.
-   Transaction search/filtering and audit history.
-   CSV/Excel export and PDF export that respect selected filters.
-   CSV migration with preview/validation, import batch report,
    duplicate detection, and reconciliation.
-   Automatic backup where hosting supports scheduled tasks, plus manual
    backup download.
-   Custom Neo-Brutalism admin UI using Blade/Tailwind, no Filament.

## Out of scope

-   Multiple bank/cash accounts or inter-account transfers.
-   Bank API or automatic bank statement synchronization.
-   Multi-currency.
-   Full double-entry accounting/general ledger.
-   Tax, payroll, invoicing automation, purchase orders, approval
    workflows, multi-user roles.
-   Transaction planning/scheduled transactions.
-   Complex project management.
-   Email reminders for due bills; dashboard notices only.

## Core business rules

-   Currency: IDR only. Timezone: Asia/Jakarta.
-   Use transaction date (actual receipt/payment date) for realized cash
    reporting. Store creation/update timestamps separately.
-   Actual cash balance is derived from opening/validated history plus
    realized cash transactions and explicit reconciliation adjustments
    where approved; never include unpaid bills.
-   Initial migration balance follows only validated CSV transaction
    history. Report differences against current actual bank balance; do
    not silently force balances to match.
-   Capital contributions are income only when an actual contribution
    occurred.
-   A refund/return of money should be categorized according to its
    actual nature; don't infer capital or income from description alone.
-   A party can be both a client and vendor. Store one primary PIC
    (name, email/phone as available).
-   Project association is optional for general company expenses, but
    mandatory when the transaction/document is project-related.
-   All financial edits, archival, payment recording, and reconciliation
    changes must be auditable.
