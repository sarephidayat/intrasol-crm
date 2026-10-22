# Migration and Data Transition Plan

## 1. Rule
Do not run migrations or alter data during repository audit. After explicit approval, migrations must be incremental, reversible where practical, and reviewed before execution.

## 2. Audit before migration
Inspect:
- current migration files and migration status without changing the database;
- existing tables, relationships, indexes, constraints, and data volumes where safe;
- current production/staging deployment process;
- backup availability and restore procedure;
- existing cash spreadsheet columns, categories, accounts, and historical coverage if the user supplies the file;
- whether the initial state should use historical imports or approved opening balances.

Never display credentials or sensitive data in audit output.

## 3. Migration principles
- Prefer additive migrations first.
- Avoid renaming/dropping columns until dependencies and data transformations are verified.
- For destructive or large transformations, prepare a backup and explicit rollback/recovery plan.
- Backfill in bounded, repeatable batches if the data volume requires it.
- Keep schema migrations separate from bulk business-data imports.
- Do not insert made-up accounting history to make reports look populated.
- Use idempotent import logic and report skipped/invalid rows.
- Preserve source references for imported transactions.
- Test migration and rollback/recovery on a copy or staging environment before production.

## 4. Accounting opening position
Before accounting reports are relied upon, agree on:
- opening balances and effective date;
- chart of accounts and account mappings;
- treatment of historical cash transactions;
- treatment of unpaid invoices and outstanding receivables/payables;
- retained earnings/opening equity handling;
- financial period boundaries and locking;
- who reviews and signs off reconciliation.

Do not claim the financial statements are complete or accurate until opening balances and mappings reconcile.

## 5. Spreadsheet import
If historical data is imported:
1. Obtain the original spreadsheet and document its columns.
2. Map each source field to the target schema.
3. Validate date formats, signs/directions, amounts, categories, duplicate references, and missing values.
4. Produce a dry-run summary: rows read, rows importable, rows skipped, and validation errors.
5. Obtain approval before executing the import.
6. Reconcile imported totals to the source spreadsheet.
7. Keep an import log and source reference for traceability.
