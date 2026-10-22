# Product Requirements — INTRASOL Management System

## 1. Product summary
An internal-only web application for PT Integrasi Solusi Teknologi (INTRASOL) to manage prospects/clients, projects, Kanban tasks, team workload, cash, invoices/payments, and foundational double-entry accounting.

## 2. Confirmed decisions
- Organization size: approximately 4–10 people.
- Project types: websites, applications, ERP implementation, and consulting.
- Lifecycle: prospect through proposal/win, delivery, billing/collection, handover, and maintenance.
- Project managers coordinate tasks, team members, and deadlines. Major decisions remain with the director.
- Task workflow: Kanban.
- Team members may work on multiple projects; provide workload visibility.
- Accounting scope: chart of accounts, journal entries, general ledger, trial balance, profit and loss, balance sheet, and cash flow for internal reporting.
- No transaction approval workflow in the MVP.
- No timesheets in the MVP.
- Internal users only; no client portal.
- Notifications: in-app and email.
- Target stack: Laravel, Livewire + Blade, MySQL; evaluate Filament where suitable.
- Authentication: email and password.
- Deployment target: Laravel-compatible hosting/cPanel.
- UI: clean, minimal, Linear-inspired; light and dark themes; INTRASOL brand colors; collapsible sidebar.
- Delivery approach: MVP first, with repository audit and plan approval before changes.

## 3. Primary user roles
- Director: full visibility and control; owns major decisions and company-level reporting.
- Project Manager: manages assigned projects, tasks, schedules, and project team; access limited by policy.
- Team Member: views assigned projects/tasks and updates permitted task status/details.
- Finance: manages cash, invoices, payments, and accounting functions if this role is approved as separate.
- Administrator: manages users, roles, configuration, and system maintenance.

Role names and permissions must be reconciled with existing code and confirmed before implementation. Do not assume every role needs to be a separate account type.

## 4. MVP modules
1. Dashboard: key operational and financial indicators, overdue tasks, upcoming deadlines, receivables, and cash position.
2. Basic CRM: prospects, clients, contacts, interaction notes, lead status, and proposal/opportunity tracking.
3. Projects: client, scope/description, status, PM, team members, planned dates, milestones, and related commercial records.
4. Tasks: Kanban board, assignee(s) if supported, priority, status, due date, comments/activity, and project association.
5. Cash: cash/bank accounts, categorized inflows/outflows, transaction date, amount, reference, notes, and attachments if feasible.
6. Invoices and payments: invoice records, line items, issue/due dates, status, payment records, and outstanding balance.
7. Accounting foundation: chart of accounts, balanced journal entries, ledger, trial balance, P&L, balance sheet, and cash flow report.
8. Team and workload: active assignments and cross-project workload visibility.
9. Notifications: in-app and email events for task assignments, due dates, project updates, invoices, and relevant mentions.
10. Administration/audit: user access, configuration, and auditable history for sensitive operations.

## 5. Core lifecycle
Prospect → qualified opportunity → proposal → won/contracted → project setup → delivery/tasks → invoice → payment/collection → handover → maintenance (where applicable).

Not every project must use every stage. Keep lifecycle transitions explicit and avoid making an invoice automatically imply revenue recognition without an approved accounting policy.

## 6. MVP exclusions unless separately approved
- Client login/portal.
- Timesheets and billable-hour tracking.
- Automated tax filing or tax compliance.
- Payroll and HR.
- Full procurement or inventory.
- Complex approval workflows.
- External accounting integrations.
- AI features.

## 7. Success criteria
- Staff can manage client/project/task information without maintaining duplicate spreadsheets for the same workflow.
- Director can see project status, overdue work, cash position, invoices, and receivables from a dashboard.
- Financial reports reconcile to posted journal entries and the chart of accounts.
- Access is enforced server-side.
- Core workflows are covered by automated tests.
- Deployment and backup/restore procedures are documented.
