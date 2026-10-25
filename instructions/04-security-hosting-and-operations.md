# Security, Hosting, and Operations

## Authentication

-   Single administrator using email/password.
-   Use Laravel's supported authentication/password reset mechanisms
    compatible with the existing Laravel version.
-   Configure email delivery through hosting SMTP settings; do not
    hard-code credentials.
-   Apply login throttling and secure session settings.
-   Do not expose public registration unless explicitly required;
    provision the initial administrator securely.

## Authorization and uploads

-   Protect every admin route with authentication middleware.
-   Validate all request data server-side.
-   Protect against CSRF, mass assignment, unsafe file upload, and
    unauthorized receipt downloads.
-   Store receipts outside the public web root/private disk when
    possible. If hosting constraints require another approach, use
    randomized filenames and authorized controller downloads.
-   Never commit `.env`, credentials, private receipts, or production
    data to version control.

## Audit

-   Audit create/update/archive/payment/reconciliation/import and
    restoration events.
-   Capture actor, timestamp, entity, action, before/after values where
    appropriate, and reason.
-   Do not log passwords, reset tokens, or secrets.
-   Financial records and audit events must not be hard-deleted from
    ordinary UI.

## Backups

-   Provide a manual backup download restricted to the administrator.
-   Configure scheduled database backup using cPanel cron/Laravel
    Scheduler where supported.
-   Document retention and restore steps. A backup is not considered
    complete until restore has been tested.
-   Do not store backups in a publicly accessible directory.
-   If automatic backup cannot be configured in the available hosting
    plan, clearly document the required cron command and remaining
    manual step; do not claim it is active.

## Scheduler and shared hosting

-   Do not require a permanent queue worker.
-   Dashboard overdue status should be computed from due date and
    outstanding amount, so correctness does not depend on scheduled
    jobs.
-   Scheduler may be used for backups or cleanup if cPanel cron is
    available.
-   Document cron setup, PHP binary/path variations, writable storage,
    symlink requirements, and deployment steps based on the actual
    hosting environment.

## Production configuration

-   `APP_DEBUG=false`, secure `APP_KEY`, HTTPS, least-privilege database
    user, secure cookies where HTTPS is enabled, correct filesystem
    permissions.
-   Validate environment configuration without printing secrets.
-   Add health checks that do not expose sensitive system details.
