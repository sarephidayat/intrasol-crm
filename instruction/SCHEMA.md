# Conceptual Data Model

This is a starting model, not permission to create migrations before the repository audit and approval. Reuse and adapt existing tables where sensible.

## 1. Identity and access
- users
- roles/permissions (existing implementation or selected package)
- user_preferences (theme and UI preferences, if needed)
- audit_logs/activity_logs

## 2. CRM
- clients: name, type, contact details, address, status, notes
- contacts: client_id, name, role, email, phone
- opportunities: client_id, title, source, stage, owner_id, estimated_value, target_date
- opportunity_activities: opportunity_id, user_id, type, body, occurred_at
- proposals/contracts: opportunity/project linkage, reference, date, amount, status, file reference (if in scope)

## 3. Project delivery
- projects: client_id, name, code, description, status, project_manager_id, start_date, target_date, completed_at
- project_members: project_id, user_id, role/assignment metadata
- milestones: project_id, title, due_date, status
- tasks: project_id, title, description, status, priority, due_date, created_by, completed_at
- task_assignees: task_id, user_id (if multi-assignee is supported)
- task_comments/activity: task_id, user_id, body/event data
- project_documents: project_id, metadata and storage path (if included)

## 4. Cash and banking
- financial_accounts/cash_accounts: name, type, currency, opening balance/date, active flag
- cash_transactions: account_id, transaction_date, direction, amount, reference, note, status, created_by
- transaction categories/mappings: category and associated chart-of-account mapping
Avoid duplicate sources of truth if cash movements are posted through accounting journals.

## 5. Billing
- invoices: client_id, project_id (nullable if needed), invoice_number, issue_date, due_date, status, subtotal, tax fields only if policy is approved, total, currency, notes
- invoice_items: invoice_id, description, quantity, unit_price, line_total
- payments: invoice_id or payment allocations, payment_date, amount, method, reference, financial_account_id, status
- payment_allocations if one payment can cover multiple invoices or one invoice can have multiple allocations

## 6. Accounting
- chart_of_accounts: code, name, account_type, parent_id, normal_balance, is_active, report_group
- accounting_periods: start_date, end_date, status/lock state
- journal_entries: entry_number, entry_date, description, source_type/source_id, status (draft/posted/reversed), created_by, posted_by, posted_at
- journal_lines: journal_entry_id, account_id, description, debit, credit
- optional reversal/adjustment linkage to preserve history

## 7. Notifications
- notifications (Laravel database notifications or equivalent)
- email delivery/queue configuration according to hosting capabilities

## 8. Schema design requirements
- Money fields use DECIMAL with an agreed precision/scale; never FLOAT/DOUBLE for amounts.
- Add foreign keys, unique constraints, and indexes based on real query patterns.
- Define deletion behavior intentionally; financial history must not cascade-delete unexpectedly.
- Use timestamps consistently and soft deletes only where appropriate.
- Use database constraints plus application-level validation for financial invariants.
- Decide currency handling, opening balances, accounting period locking, invoice numbering, and spreadsheet import strategy before finalizing the schema.
- During audit, compare this conceptual model to the actual schema and report deltas. Do not blindly generate every table listed here.
