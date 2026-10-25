# Historical CSV Import and Migration Specification

## Source file characteristics observed

The supplied file `Kas INTRASOL - Kas.csv` is a spreadsheet-style
export, not a clean rectangular transaction table. It includes: - A
title and summary section before the table header. - Summary totals for
income, expenses, and current balance. - Transaction rows with columns
resembling date, description, legacy category, income, expense, and
running balance. - Additional report/widget columns to the right of the
transaction table. - Dates in more than one format, including dates
without a year such as `14 Juli` and `7 September`. - Legacy categories
that do not match the new category list exactly. - A final row
describing a bank/cash movement whose meaning and amount direction must
be reviewed rather than inferred.

Treat this as a source-data observation, not a guarantee that every
future CSV will have the same layout.

## Import design

-   Allow selecting/uploading a CSV and parsing it without mutating live
    financial records.
-   Use a preview stage showing source row number, raw date, parsed date
    (if unambiguous), description, legacy category, income, expense,
    running balance, classification, and validation messages.
-   Classify rows:
    -   `valid_transaction`
    -   `summary_or_report`
    -   `ambiguous`
    -   `invalid`
    -   `likely_duplicate`
-   Import only rows explicitly confirmed as valid by the administrator.
-   Keep the original raw row/source values and source row number in
    import tracking.
-   Compute a deterministic row fingerprint from normalized raw source
    fields plus source file identity; report collisions and likely
    duplicates.
-   Make repeated import safe and idempotent.
-   Do not trust or import summary totals as transactions.
-   Keep source category and map to a canonical category separately.
-   Allow the admin to map unknown legacy categories before import.
-   Never infer missing year, transaction direction, or missing amount.
    Request manual resolution or skip.
-   If both income and expense are populated, flag the row for review
    unless the import format explicitly supports a valid split
    transaction.
-   Validate date and amount formats, currency symbols/separators,
    negative values, and running balance arithmetic.
-   Do not assume running-balance inconsistencies mean a new adjustment
    transaction should be created.

## Reconciliation report

After import, show: - number of rows scanned - valid rows imported -
rows skipped as summary/report - ambiguous/invalid rows requiring
review - duplicate rows skipped - total imported income - total imported
cash expense - calculated final balance from validated starting point
and imported cash transactions - source summary totals for comparison
(clearly labeled as source-reported) - difference from current actual
Bank Jago balance if the admin provides it

The historical CSV total and current bank balance may represent
different dates. Do not label their difference as an error without
considering the date range and any subsequent bank movements.

## Specific review flags from the provided file

-   Summary/title/report lines must not be imported.
-   Dates such as `14 Juli` and `7 September` lack a year and must be
    flagged.
-   The row `Pindah bank untuk kas intrasol` requires human review to
    establish whether it represents a real expense, a transfer, or a
    bookkeeping note. Since the current system has one cash account and
    no inter-account transfer feature, do not import it as a normal
    expense without confirmation.
-   Preserve legacy categories such as `Partner`, `Administrasi`,
    `Learning`, `Gaji Developer`, `Gathering`, and `Pengeluaran Project`
    for review/mapping. Do not silently collapse categories based on
    names alone.
-   The source contains an annotation about a director/owner capital
    amount. Do not treat annotations as cash transactions unless a
    corresponding transaction row and direction are confirmed.

## Operational safety

-   Back up the database before committing a production import.
-   Prefer a dry-run/preview.
-   Use a database transaction per confirmed batch or a carefully
    designed chunk strategy with resumable status.
-   Show row-level errors and allow corrected re-import without
    duplicating already-imported records.
-   Never delete pre-existing records to make imported totals match.
