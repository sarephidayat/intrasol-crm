# Architecture and Implementation Guidance

## 1. Target approach
Use a Laravel modular monolith with MySQL. Keep deployment straightforward for a Laravel-compatible cPanel environment. Do not split into microservices for this company size/MVP.

## 2. Frontend
- Laravel Blade for page structure and reusable layout.
- Livewire for interactive workflows such as Kanban, filters, forms, and dashboard widgets.
- Evaluate Filament for internal CRUD/admin screens, tables, and forms.
- Do not create duplicate UI patterns by mixing multiple approaches without a clear reason.
- Support responsive layouts, collapsible sidebar, light/dark theme, and accessible keyboard/focus states.

## 3. Suggested domain modules
- Identity & Access
- CRM
- Projects & Delivery
- Team & Workload
- Cash & Banking
- Billing
- Accounting
- Notifications
- Audit & Administration

Modules can share the same Laravel application/database, but keep models, services, policies, and tests organized by domain.

## 4. Layering
Use a practical separation:
- UI: Blade/Livewire/Filament resources.
- Validation: Form Requests or Livewire validation rules.
- Authorization: policies/gates and role/permission checks.
- Application/domain services: multi-step use cases and business invariants.
- Persistence: Eloquent models and relationships.
- Reporting: query/services dedicated to report calculations.
Avoid an unnecessary abstraction for every model, but do not put accounting posting logic inside UI components.

## 5. Financial write operations
- Use database transactions for posting journals, recording payments, and creating linked financial records.
- Validate account status, currency, period status, and balanced debit/credit totals before posting.
- Prevent duplicate payment posting and duplicate journal posting.
- Use decimal database types for money; never floating-point types for monetary amounts.
- Store timestamps consistently and format them for the user's locale/timezone.
- Define a clear distinction between draft and posted accounting records.

## 6. Security
- Enforce authorization on the server for every sensitive read/write.
- Validate uploads, size, MIME type, and storage access.
- Protect against mass assignment, CSRF, XSS, insecure direct object references, and unauthorized file downloads.
- Use rate limiting for authentication-sensitive endpoints.
- Do not log secrets or full sensitive payloads.
- Audit important changes, especially posted journals, payment changes, user/role changes, and deletion/void actions.

## 7. cPanel deployment
During audit, confirm PHP version/extensions, Composer availability, Node/build requirements, document root configuration, queue/cron support, mail transport, storage permissions, and MySQL version. Do not assume all capabilities exist.
Prefer a deployment process that builds assets before upload when Node.js is unavailable in production. Document storage symlink requirements and queue fallback if a persistent worker is unavailable.

## 8. Architecture decisions to surface
Do not finalize these silently:
- Existing app architecture and reuse opportunities.
- Whether Filament fits the installed Laravel version and current frontend.
- Role/permission package or existing authorization system.
- Email transport and queue strategy.
- Currency and accounting period/closing policy.
- How historical spreadsheet data will be handled.
