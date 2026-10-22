# INTRASOL Management System — Agent Instructions

## 1. Purpose
Build an internal company management system for PT Integrasi Solusi Teknologi (INTRASOL), following the product and technical decisions in `PRD.md`, `ARCHITECTURE.md`, `DESIGN.md`, `SCHEMA.md`, `RULES.md`, `MIGRATION.md`, and `TESTING.md`.

## 2. Mandatory workflow — do not skip
### Phase 0: Repository audit (read-only)
Before changing anything:
1. Read this file and every instruction document in this folder.
2. Inspect the repository structure and current Git status.
3. Identify the existing framework/runtime versions, dependencies, frontend stack, auth implementation, routes, controllers, models, migrations, policies, tests, assets, and deployment setup.
4. Compare the current implementation against the requirements.
5. Report:
   - current state and reusable components;
   - gaps and risks;
   - proposed architecture and implementation sequence;
   - database/migration risks;
   - testing strategy;
   - estimates by phase;
   - only the questions that block a safe plan.
6. Stop and wait for explicit approval from the user.

### Phase 1: Approved plan
Only after approval, refine the backlog into small, testable tasks. Confirm the first implementation slice and expected files to change.

### Phase 2: Incremental implementation
Implement one vertical slice at a time. After each slice:
- run relevant tests and static/build checks;
- review authorization and validation;
- report files changed, behavior added, tests run, and known limitations;
- do not silently broaden scope.

### Phase 3: Release readiness
Verify production build, migrations, authorization, backups/restore approach, deployment instructions, and acceptance criteria before recommending release.

## 3. Strict no-change rule during audit
Until the user explicitly approves the implementation plan, do NOT:
- edit, create, rename, or delete project files;
- install/update packages or run commands that modify dependency lockfiles;
- run migrations, seeders, database writes, or destructive commands;
- reformat the repository;
- begin coding “just to test” an approach.

Read-only inspection commands are allowed. Never expose `.env` secrets, API keys, passwords, tokens, private keys, or database credentials in reports.

## 4. General engineering rules
- Prefer existing project conventions over introducing a new stack.
- Use Laravel + MySQL as the target unless repository audit shows a material constraint.
- Prefer a modular monolith and small, cohesive modules.
- Use Laravel Livewire + Blade; assess Filament for admin/internal CRUD where it fits, rather than forcing it everywhere.
- Keep business rules out of presentation components.
- Use Form Requests or equivalent validation, policies/gates for authorization, transactions for multi-record financial writes, and explicit error handling.
- Never trust hidden UI controls as authorization.
- Do not invent business decisions where the specification marks an assumption as open; surface it for approval.
- Keep the application compatible with the actual target cPanel/hosting capabilities.

## 5. Communication
Be concise and concrete. State assumptions explicitly. Do not claim tests passed unless they were run and their output was checked. If blocked, explain the exact blocker and the minimum decision needed.
