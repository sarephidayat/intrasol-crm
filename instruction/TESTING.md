# Testing and Acceptance Strategy

## 1. Test layers
- Unit tests for domain calculations and accounting invariants.
- Feature tests for routes, validation, authorization, and key user workflows.
- Database tests for relationships, unique constraints, and transaction behavior.
- UI/build checks for Livewire/Blade/Filament interactions and frontend assets.
- Manual smoke tests for deployment-specific behavior on cPanel/staging.

Use the repository's existing test framework and conventions where possible. Do not introduce a new testing stack without justification.

## 2. Critical accounting tests
- Posting a balanced journal succeeds.
- Posting an unbalanced journal fails and persists no partial data.
- Draft journals may remain unbalanced if the chosen workflow permits it.
- Inactive or invalid accounts cannot be used for a new posted journal.
- Posted journals cannot be silently edited/deleted.
- Reversal/correction entries preserve auditability.
- Trial balance debits equal credits for the selected scope.
- P&L totals match posted income/expense lines in the selected period.
- Balance sheet balances according to the approved report logic.
- Cash/bank transfers do not inflate income or expense.
- Financial amounts are not calculated using binary floating-point arithmetic.

## 3. Critical billing tests
- Invoice totals are computed server-side.
- Partial payment updates outstanding balance correctly.
- Overpayment is rejected unless policy explicitly permits it.
- Duplicate payment posting is prevented.
- Invoice status remains consistent with amount paid, total, and due date.
- Invoice creation does not create a cash receipt without a payment event.

## 4. Authorization tests
- Unauthenticated users cannot access protected routes.
- Team members cannot access director/finance-only operations unless granted.
- PMs can manage only the projects/actions permitted by their policy.
- Direct URL/API/Livewire requests cannot bypass authorization.
- File downloads require permission.
- User/role changes are restricted and auditable.

## 5. Operational workflow tests
- Create/update a client and opportunity.
- Convert an approved/won opportunity into a project without duplicate records.
- Assign PM and project team members.
- Create, assign, move, and complete tasks.
- Display workload across multiple projects.
- Record cash inflow/outflow and filter transactions.
- Create invoice and record one or more payments.
- Display relevant notifications and handle email failure safely.

## 6. UI acceptance
- Sidebar collapses/expands and active navigation is clear.
- Light and dark themes remain readable.
- Forms show validation errors near relevant fields.
- Empty/loading/error states are understandable.
- Key screens work at desktop and narrow viewport widths.
- Keyboard focus and labels are usable.
- Tables and filters do not expose data the user is not authorized to see.

## 7. Release gate
Do not claim release readiness until:
- automated tests pass or failures are documented;
- production assets build successfully;
- migrations have been reviewed and tested;
- authorization and financial invariants have test coverage;
- backup/restore and deployment instructions are documented;
- known limitations and unresolved business decisions are visible.
