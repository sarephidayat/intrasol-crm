# UI/UX Design System --- Neo-Brutalism

## Reference direction

Use the user's supplied screenshot as the primary layout reference: a
clean admin dashboard with left sidebar, topbar, clear page title, data
cards, charts, and tables. Adapt the layout to cash management; do not
copy project-management features.

## Visual identity

-   Neo-Brutalism: crisp, high-contrast, purposeful.
-   Core palette:
    -   Ink: `#171717`
    -   Warm off-white: `#F7F5EF`
    -   Yellow: `#F5E64D`
    -   Mint: `#B9F4C8`
    -   Positive green: `#166534`
    -   Negative red: `#B42318`
    -   White: `#FFFFFF`
    -   Muted border/surface: `#DDDDDD`
-   Strong black borders (typically 2px for key cards/buttons, 1px for
    table dividers).
-   Hard shadows (e.g. `4px 4px 0 #171717`) used selectively, not on
    every component.
-   Square or slightly rounded corners; avoid pill-heavy UI.
-   Use a distinctive, legible heading font such as Space Grotesk and a
    highly readable body font such as Inter, if loading these fonts is
    feasible. Provide robust system fallbacks.

## Layout

-   Reusable `resources/views/layouts/app.blade.php` for authenticated
    pages.
-   Reusable guest/auth layout for login and password reset.
-   Reusable Blade components for sidebar, topbar, flash messages, stat
    cards, form fields, status badges, confirmation dialogs, and
    pagination.
-   Sidebar on desktop; responsive drawer on mobile with accessible
    open/close behavior.
-   Active navigation based on named routes, not hard-coded URL
    substring checks.
-   Main content area must handle tables/forms without horizontal
    overflow where practical.
-   Menus: Dashboard, Transaksi, Tagihan (Piutang & Utang), Dana
    Talangan, Master Data (Klien/Vendor and Proyek), Kategori,
    Rekonsiliasi, Laporan, Riwayat Aktivitas, Pengaturan.
-   Hide or omit menu items that are genuinely not implemented; do not
    show decorative non-functional links.

## Dashboard hierarchy

1.  Heading "Dashboard Kas" and current reporting period.
2.  Summary cards: actual cash balance, current-month income,
    current-month cash outflow, current-month net cash movement.
3.  Annual Jan--Dec chart comparing income and cash outflow.
4.  Receivables/payables due soon and overdue summary.
5.  Recent cash transactions.
6.  Reconciliation difference/status if a recent reconciliation exists.

## Data-entry UX

-   Full form is requested, but organize it into clear sections and show
    conditional fields only when relevant.
-   Make income/expense type visually distinct.
-   Use explicit labels and helper text; don't rely on placeholders
    alone.
-   Amounts formatted as IDR in UI, stored as integer rupiah.
-   Keep validation errors near the relevant fields and preserve entered
    data after validation failure.
-   Receipt upload is optional. Restrict allowed MIME types/extensions
    and size; store privately where possible, authorize every download.
-   Confirm destructive/archive/correction actions and capture audit
    reasons where appropriate.

## Charts and tables

-   Charts must have readable labels, currency formatting, accessible
    color contrast, and empty states.
-   Do not use charts as decoration. Annual chart must compare each
    calendar month's realized income and cash outflow.
-   Reports must clearly distinguish cashflow from unpaid bills and
    director-advance expenses.
-   Tables should have search, relevant filters, pagination, empty
    state, and clear actions.
-   Provide responsive alternatives for small screens.

## Avoid

-   Generic gradients, glassmorphism, oversized rounded cards, random
    animation, fake metrics, excessive decorative icons, and fake
    activity.
-   Do not hard-code demo financial values in production UI.
-   Keep animations subtle and respect reduced-motion preferences.
