# Business Rules and Invariants

## 1. General
- Every record must have an explicit ownership/access rule.
- Users can only perform actions allowed by their role and the record's policy.
- Important changes should be traceable through activity/audit records.
- Deleting business-critical records should generally be replaced by archive, void, or cancellation semantics where appropriate.

## 2. CRM and projects
- A project should link to a client when the client is known.
- A project has a responsible project manager and may have multiple team members.
- A user can participate in multiple projects.
- Project status transitions must be explicit and visible.
- Tasks belong to a project unless an approved use case supports standalone tasks.
- Due dates, priority, assignee, and task status must be filterable.
- Closing or archiving a project must not silently delete its financial records.

## 3. Cash
- Record transaction date, account, direction, amount, category/account mapping, reference, and note where relevant.
- Money values use fixed-precision decimal storage.
- A cash transaction is not automatically equivalent to revenue or expense recognition.
- Transfers between cash/bank accounts must not be counted as income or expense.
- Reversals/voids must preserve traceability rather than silently rewriting posted history.
- The system must not assume a transaction is approved just because no approval workflow is required.

## 4. Invoices and payments
- Invoice totals must be calculated server-side from validated line items.
- A payment cannot exceed the remaining balance unless an explicitly approved overpayment/credit policy exists.
- Support partial payments if required by the workflow.
- Invoice status should be derived or reconciled from dates, totals, and payment records; avoid inconsistent manual statuses.
- Do not treat invoice creation as proof that cash was received.
- Prevent duplicate payment recording/posting.

## 5. Double-entry accounting
- Every posted journal entry must balance: total debits equal total credits.
- Draft entries may be incomplete; posted entries may not.
- Posted entries should not be silently edited or deleted. Use reversal/correction entries according to an approved policy.
- Journal lines must reference valid, active accounts in the chart of accounts.
- A report period must have a defined date range.
- Trial balance must reconcile to posted journal lines.
- P&L includes income and expense accounts for the selected period.
- Balance sheet reports asset, liability, and equity balances as of a date.
- Cash flow must use a documented classification and reconciliation method.
- Do not invent accounting, tax, revenue recognition, or period-close policy. Flag decisions requiring director/accountant input.

## 6. Data integrity
- Use foreign keys and appropriate indexes.
- Use database transactions for multi-step financial operations.
- Use unique constraints for identifiers such as invoice numbers within the agreed scope.
- Protect against race conditions when recording payments or posting journals.
- Validate authorization at the server/service layer, not only by hiding buttons.
