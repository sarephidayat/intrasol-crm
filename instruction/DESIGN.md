# UI/UX Design Direction

## 1. Visual direction
- Clean, minimal, high-signal interface inspired by the information density and workflow clarity of Linear; do not copy its branding or exact proprietary design.
- Use INTRASOL logo/brand colors as accents after locating the actual logo or brand assets in the repository.
- Light and dark themes.
- Collapsible left sidebar; clear active navigation state.
- Comfortable typography, consistent spacing, restrained borders/shadows, clear hierarchy.
- Avoid excessive gradients, decorative animation, and oversized dashboard cards.

## 2. Main navigation proposal
- Overview
- CRM
- Projects
- Tasks
- Team
- Finance
  - Cash & Bank
  - Invoices
  - Payments
  - Accounting
  - Reports
- Notifications
- Settings

Adjust navigation based on actual roles and modules available to the user.

## 3. Key screens
### Dashboard
Show actionable items first: overdue tasks, due soon, project health, receivables due, current cash/bank balances, and recent activity. Do not show misleading metrics with unclear definitions.

### CRM
Table/list with search, filters, status, company/contact, owner, and next action. Use a detail view for notes and opportunity history.

### Projects
List and detail pages with client, PM, team, status, target dates, milestones, tasks, and financial links.

### Kanban tasks
Columns reflect configured task states. Support task detail, priority, assignee, due date, comments, and activity. Drag-and-drop is optional; if implemented, persist transitions safely and check permissions.

### Cash
Make entering a transaction fast: date, account, type (in/out), category/account mapping, amount, reference, and note. Make filters and export/reporting practical. Avoid treating a cash transaction as an accounting journal unless the posting rules are defined.

### Invoices
Show invoice number, client, issue date, due date, total, paid amount, balance, and status. Make partial payments explicit.

### Accounting reports
Readable period filters and report totals. Include drill-down from report rows to ledger/journal details where practical. Reports must be generated from posted accounting entries or a documented reconciliation approach.

## 4. Interaction rules
- Prefer inline filters, clear empty states, useful loading states, and actionable error messages.
- Confirm destructive or irreversible actions.
- Use optimistic UI only where rollback/error handling is safe.
- Show validation near the relevant field.
- Make keyboard navigation and focus indicators usable.
- Keep tables responsive; define mobile behavior rather than allowing uncontrolled overflow.

## 5. Components and consistency
Create a small design system: buttons, inputs, selects, badges, status indicators, dialogs, tables, cards, page headers, filters, empty states, and toast/notification patterns. Reuse existing components where they are sound.

## 6. Theme and accessibility
- Theme switch should persist using the app's supported mechanism.
- Maintain readable contrast in both themes.
- Do not use color alone to convey status.
- Respect reduced-motion preferences.
- Inspect actual INTRASOL assets before choosing exact accent colors; do not guess the logo palette.
