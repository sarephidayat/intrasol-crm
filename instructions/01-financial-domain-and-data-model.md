# Financial Domain and Data Model

This document describes the logical model. Inspect the repository and
current Laravel conventions before choosing exact migration names/types.

## Recommended entities

### users

Single administrator account. Standard Laravel authentication fields and
password reset support.

### parties

Reusable clients/vendors. - id - name - role: `client`, `vendor`,
`both` - primary_pic_name - primary_pic_email nullable -
primary_pic_phone nullable - address/notes nullable if useful -
is_active - timestamps, soft delete if appropriate

### projects

Simple project directory only. - id - name - client_party_id nullable -
description/reference nullable - status (active/archived) only if
useful - timestamps Do not add tasks, milestones, assignments, or
project workflow.

### categories

-   id, name, transaction_type (`income` or `expense`), is_active,
    timestamps Prevent category-type mismatch. Seed sensible defaults
    but allow the admin to manage categories.

Suggested default income categories: Project Income, Other Income, Owner
Capital. Suggested expense categories: Operational, Project Expense,
Subscription, Transport, Salary, Other. Preserve legacy source
category/description during import, even when mapped to a new category.

### cash_transactions

Represents realized cash movements only. - id - transaction_date - type:
`income` or `expense` - amount as integer rupiah (avoid floating
point) - category_id - party_id nullable - project_id nullable - source:
manual, receivable_payment, payable_payment, director_advance,
director_reimbursement, migration, reconciliation_adjustment -
source_reference nullable (linked document/payment/advance) -
description - receipt_path nullable - archived_at nullable -
created_by/updated_by if practical for single-user traceability -
timestamps Use a database transaction when created from a payment. A
transaction created from a payment must be linked uniquely to that
payment.

### bills

Use one shared table for receivables and payables. - id - direction:
`receivable` or `payable` - party_id - project_id nullable -
bill_number/reference nullable - description - issue_date - due_date -
amount integer IDR - status derived or persisted carefully: unpaid,
partially_paid, paid, archived - notes nullable - timestamps,
archived_at nullable A bill associated with a project must have
project_id. Consider enforcing this through request/domain validation.

### bill_payments

-   id
-   bill_id
-   payment_date
-   amount integer IDR
-   payment_method/source (for current MVP, Bank Jago for company-funded
    movements; director-paid expenses handled through advances)
-   cash_transaction_id nullable only if a payment does not affect
    company cash (e.g. company expense paid personally); otherwise
    required after successful posting
-   notes nullable
-   timestamps Enforce amount \> 0 and total payments \<= bill amount.
    Use transaction/locking to avoid concurrent overpayment. A
    receivable payment creates an income cash transaction; a payable
    payment creates an expense cash transaction. The transaction and
    payment must commit atomically.

### director_advances

Tracks company expenses paid by the director using personal money. -
id - transaction_date - description - category_id (expense category) -
project_id nullable - amount integer IDR - reimbursed amount or derive
from reimbursements - receipt_path nullable - status derived:
outstanding, partially_reimbursed, reimbursed - timestamps, archived_at
nullable A director-paid expense is an expense for reporting but does
not reduce Bank Jago cash balance at the time of purchase. Decide in
implementation whether to represent it as a non-cash expense record
linked to a cash transaction model or as an expense record with
`affects_cash=false`; do not make it an ordinary cash expense that
reduces bank balance. Director reimbursement creates a cash expense and
reduces liability, not a second operating expense.

### director_advance_reimbursements

-   id, director_advance_id, payment_date, amount, cash_transaction_id,
    notes, timestamps Reimbursement creates a linked cash expense
    transaction. It should not count again in operating
    expense-by-category/project reports if the original advance already
    counted as expense. Provide separate cashflow/liability reporting as
    needed.

### reconciliations

-   id
-   reconciliation_date
-   system_balance
-   actual_bank_balance
-   difference
-   notes
-   status (reviewed/posted) if adjustment workflow exists
-   timestamps Do not automatically generate income/expense for
    difference. For MVP, reconciliation can record and report the
    difference without changing the ledger; any adjustment must be
    explicit, reasoned, and auditable.

### audit_logs

Append-only audit events for financial records. - user_id nullable -
auditable_type, auditable_id - action: created, updated, archived,
payment_recorded, reconciled, imported, restored -
before_values/after_values JSON where safe - reason nullable -
created_at Do not log passwords, reset tokens, or unnecessary sensitive
data. Application-level append-only behavior is required; ordinary UI
must not edit/delete audit records.

### import_batches / import_rows (recommended)

Track import source, file hash, row number, raw values, status,
validation errors, mapped record ID, duplicate fingerprint, imported
timestamp. This supports preview, reruns, and auditability.

## Financial calculation rules

-   Amounts are integer rupiah, never float.
-   `outstanding = bill_amount - sum(successful bill payments)`.
-   Status: unpaid if paid sum = 0; partially_paid if 0 \< paid sum \<
    bill amount; paid if sum = bill amount.
-   Overdue is derived when outstanding \> 0 and due_date \< current
    date in Asia/Jakarta. Do not store a stale `overdue` state unless
    necessary.
-   Do not allow total successful payments to exceed bill amount.
-   Actual cash balance must include only cash-affecting realized
    transactions and the validated opening/history baseline.
-   Avoid double-counting bill payments, director advances, and
    reimbursements in cashflow vs expense reports. Define each report's
    basis explicitly.
