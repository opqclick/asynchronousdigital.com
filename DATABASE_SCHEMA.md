# Database Entity Relationship Diagram

## Core Entities and Relationships

```
┌─────────────┐
│    ROLES    │
├─────────────┤
│ id          │
│ name        │──────┐
│ display_name│      │
│ description │      │
└─────────────┘      │
                     │
                     │ 1:N
                     │
┌─────────────────────────────┐
│          USERS              │
├─────────────────────────────┤
│ id                          │
│ role_id (FK) ───────────────┘
│ name                        │
│ email                       │
│ password                    │
│ phone                       │
│ address                     │
│ payment_model               │
│ monthly_salary              │
│ is_active                   │
│ deleted_at                  │
└──────┬──────────────┬───────┘
       │              │
       │ M:N          │ M:N
       │              │
┌──────▼──────┐  ┌───▼──────────┐
│  TEAM_USER  │  │  TASK_USER   │
│ (Pivot)     │  │  (Pivot)     │
├─────────────┤  ├──────────────┤
│ team_id(FK) │  │ task_id (FK) │
│ user_id(FK) │  │ user_id (FK) │
│ joined_at   │  │ assigned_at  │
└──────┬──────┘  └───┬──────────┘
       │             │
       │ M:N         │ M:N
       │             │
┌──────▼─────────────────┐  ┌──────▼───────────────┐
│        TEAMS           │  │       TASKS          │
├────────────────────────┤  ├──────────────────────┤
│ id                     │  │ id                   │
│ name                   │  │ project_id (FK) ─────┐
│ description            │  │ title                │
│ is_active              │  │ description          │
│ deleted_at             │  │ status               │
└──────┬─────────┬───────┘  │ priority             │
       │         │           │ due_date             │
       │ M:N     │ M:N       │ estimated_hours      │
       │         │           │ actual_hours         │
┌──────▼─────────▼─────┐    │ order                │
│  PROJECT_TEAM        │    │ deleted_at           │
│  (Pivot)             │    └──────────────────────┘
├──────────────────────┤           │
│ project_id (FK) ─────┘           │ N:1
│ team_id (FK)         │           │
│ assigned_at          │    ┌──────▼──────────────────┐
└──────────────────────┘    │       PROJECTS          │
                            ├─────────────────────────┤
┌─────────────────────┐     │ id                      │
│      CLIENTS        │     │ client_id (FK) ─────────┐
├─────────────────────┤     │ name                    │
│ id                  │     │ description             │
│ user_id (FK) ───────┼─┐   │ tech_stack (JSON)       │
│ company_name        │ │   │ start_date              │
│ contact_person      │ │   │ end_date                │
│ email               │ │   │ status                  │
│ phone               │ │   │ billing_model           │
│ address             │ │   │ project_value           │
│ website             │ │   │ deleted_at              │
│ notes               │ │   └─────────┬───────────────┘
│ is_active           │ │             │
│ deleted_at          │ │             │ 1:N
└─────────────────────┘ │             │
       │ 1:1            │   ┌─────────▼────────────────┐
       └────────────────┼───│      INVOICES            │
                        │   ├──────────────────────────┤
                        └───│ project_id (FK)          │
                            │ client_id (FK)           │
                            │ invoice_number           │
                            │ issue_date               │
                            │ due_date                 │
                            │ subtotal                 │
                            │ tax                      │
                            │ discount                 │
                            │ total_amount             │
                            │ paid_amount              │
                            │ status                   │
                            │ notes                    │
                            │ deleted_at               │
                            └──────────┬───────────────┘
                                       │ 1:N
                                       │
                            ┌──────────▼───────────────┐
                            │      PAYMENTS            │
                            ├──────────────────────────┤
                            │ id                       │
                            │ invoice_id (FK)          │
                            │ amount                   │
                            │ payment_date             │
                            │ payment_method           │
                            │ transaction_id           │
                            │ notes                    │
                            └──────────────────────────┘


┌─────────────────────────────┐
│        SALARIES             │
├─────────────────────────────┤
│ id                          │
│ user_id (FK) ───────┐       │
│ project_id (FK)     │       │
│ month               │       │
│ base_amount         │       │
│ bonus               │       │
│ deduction           │       │
│ total_amount        │       │
│ status              │       │
│ payment_date        │       │
│ notes               │       │
└─────────────────────┘       │
                              │
                              └───────────────┐
                                              │ N:1
                                              │
                                    Back to USERS
```

## Relationship Summary

### One-to-Many (1:N)
- `roles` → `users` (One role has many users)
- `users` → `clients` (One user can be one client)
- `users` → `salaries` (One user has many salary records)
- `clients` → `projects` (One client has many projects)
- `projects` → `tasks` (One project has many tasks)
- `projects` → `invoices` (One project has many invoices)
- `clients` → `invoices` (One client has many invoices)
- `invoices` → `payments` (One invoice has many payments)
- `projects` → `salaries` (One project can have many salary records)

### Many-to-Many (M:N)
- `users` ↔ `teams` (via `team_user` pivot)
- `users` ↔ `tasks` (via `task_user` pivot)
- `teams` ↔ `projects` (via `project_team` pivot)
- `teams` ↔ `tasks` (via `task_team` pivot)

## Pivot Tables

### team_user
- Links users to teams
- A user can belong to multiple teams
- A team can have multiple users

### project_team
- Links projects to teams
- A project can be assigned to multiple teams
- A team can work on multiple projects

### task_user
- Links tasks to individual users
- A task can be assigned to multiple users
- A user can have multiple tasks

### task_team
- Links tasks to teams
- A task can be assigned to entire teams
- A team can have multiple tasks assigned

## Key Features

### Soft Deletes
Tables with `deleted_at` column:
- users
- teams
- clients
- projects
- tasks
- invoices

### Enums

**Task Status:**
- to_do
- in_progress
- review
- done

**Task Priority:**
- low
- medium
- high
- urgent

**Project Status:**
- active
- paused
- completed
- cancelled

**Project Billing Model:**
- task_based
- monthly
- fixed_price

**User Payment Model:**
- monthly
- project_based
- task_based
- contractual

**Invoice Status:**
- draft
- sent
- paid
- overdue
- cancelled

**Payment Method:**
- bank_transfer
- cash
- check
- online
- other

**Salary Status:**
- pending
- paid
- cancelled
