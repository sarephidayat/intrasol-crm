# Workflows and Acceptance Criteria

## A. Actual cash transaction

1.  Admin chooses income or expense.
2.  Enters transaction date, amount, category, description, optional
    party, optional project, and optional receipt.
3.  If marked as project-related, project is required.
4.  Validate positive integer IDR amount, valid date, category type, and
    receipt file type/size.
5.  Save and write audit event.
6.  Update dashboard/reports from persisted records, not client-side
    totals.

Acceptance: - Unpaid bills never change cash balance. - Transaction
dates use actual receipt/payment date. - A category for income cannot be
used for expense and vice versa. - Archived transactions are excluded
from active totals but remain visible in history/audit according to
documented behavior.

## B. Receivable

1.  Create receivable with client, description, issue date, due date,
    amount, optional project (required if project-related), and
    notes/reference.
2.  Dashboard shows due/overdue balances.
3.  Record a full or partial receipt.
4.  In one database transaction, create bill payment and linked income
    cash transaction.
5.  Recompute outstanding and status.

Acceptance: - Partial payment of Rp4,000,000 against Rp10,000,000
creates Rp4,000,000 income and leaves Rp6,000,000 outstanding. -
Repeated submission must not create duplicate payment or transaction. -
Payment amount cannot exceed outstanding amount.

## C. Payable

1.  Create payable with vendor, description, issue date, due date,
    amount, optional project, and notes/reference.
2.  Dashboard shows due/overdue balances.
3.  Record full or partial payment.
4.  In one database transaction, create bill payment and linked expense
    cash transaction.
5.  Recompute outstanding and status.

Acceptance: - Payment reduces cash by payment amount only. - No
duplicate expense when saving/retrying a payment. - Payment amount
cannot exceed outstanding amount.

## D. Director advance

1.  Record company expense paid by director personally.
2.  Expense is attributed to its expense category/project where
    applicable.
3.  Increase liability owed to director; do not reduce Bank Jago
    balance.
4.  Record reimbursement from company bank account when paid.
5.  Reimbursement decreases cash and liability; does not create a second
    operating expense.

Acceptance: - A Rp500,000 personally paid hosting expense increases
company expense reporting and director liability by Rp500,000 but does
not change Bank Jago cash. - Reimbursing Rp500,000 decreases Bank Jago
cash and director liability by Rp500,000 without duplicating operating
expense.

## E. Reconciliation

1.  Admin records reconciliation date and actual Bank Jago balance.
2.  System calculates ledger balance as of that date using realized
    cash-affecting movements.
3.  Show difference clearly.
4.  Store audit event and notes.
5.  Do not silently post a balancing transaction.

Acceptance: - Reconciliation does not change cash balance by itself. -
Difference is visible and exportable. - Any explicit adjustment must
have a reason and audit event.

## F. Import historical CSV

1.  Upload/select CSV.
2.  Parse and show preview.
3.  Classify rows as valid, summary/non-transaction, ambiguous, invalid,
    or likely duplicate.
4.  Map legacy categories to new categories while preserving source
    category and raw row.
5.  User confirms valid rows.
6.  Import valid rows idempotently and produce a report.
7.  Compare imported totals/running balance to source summary and
    current actual bank balance, clearly distinguishing these
    comparisons.

Acceptance: - Summary/report rows are never imported as transactions. -
Rows with dates without a year (e.g. `14 Juli`, `7 September`) are
flagged for review, not guessed. - Rows with missing amount or uncertain
direction are flagged. - Import rerun does not duplicate previously
imported rows. - Source totals are not trusted blindly; discrepancies
are displayed. - Import can proceed with valid rows while ambiguous rows
remain unresolved, if the user explicitly confirms that behavior.

## G. Audit and archive

-   Edit/archive financial records with a reason when appropriate.
-   Preserve before/after values and actor/time.
-   No hard-delete from normal UI.
-   Payment-linked records cannot be edited independently in ways that
    break the relationship; corrections must update related records
    atomically or use a documented reversal/correction workflow.

## H. Dashboard/reports

Dashboard: - current cash balance - current-month income, cash expenses,
net cash movement - annual January--December income vs expense chart -
receivables/payables due soon and overdue - recent transactions
Reports: - monthly, annual, category, project, cashflow - filters for
date range, category, party, project, direction/status as relevant -
export CSV/Excel and PDF respects current filters - report labels
distinguish cash movement from expenses recognized via director advances
and outstanding bills.
