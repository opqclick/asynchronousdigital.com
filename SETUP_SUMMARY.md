# Asynchronous Digital - Agency Management System

## 🎯 Project Overview
Custom agency management system with CRM, Task Management, Team Management, Accounting, and HR modules built with Laravel.

## ✅ Completed Setup

### 1. Database Architecture
Complete normalized MySQL database with 15 tables:

#### Core Tables
- **roles** - Admin, Team Member, Client
- **users** - All system users with role-based access
- **teams** - Team management
- **clients** - Client profiles and company information
- **projects** - Client projects with tech stack and billing models
- **tasks** - Task management with Trello-style statuses
- **invoices** - Client billing and invoicing
- **payments** - Invoice payment tracking
- **salaries** - Team member payroll management

#### Pivot Tables (Many-to-Many Relationships)
- **team_user** - Users belong to multiple teams
- **project_team** - Projects can have multiple teams
- **task_user** - Tasks assigned to multiple users
- **task_team** - Tasks assigned to entire teams

### 2. Eloquent Models with Relationships
All models created with complete relationship definitions:

#### User Model
- Belongs to: Role
- Many-to-many: Teams, Tasks
- Has many: Clients, Salaries
- Helper methods: `isAdmin()`, `isTeamMember()`, `isClient()`

#### Team Model  
- Many-to-many: Users, Projects, Tasks

#### Client Model
- Belongs to: User
- Has many: Projects, Invoices

#### Project Model
- Belongs to: Client
- Many-to-many: Teams
- Has many: Tasks, Invoices, Salaries

#### Task Model (Trello-style)
- Belongs to: Project
- Many-to-many: Users, Teams
- Statuses: `to_do`, `in_progress`, `review`, `done`
- Priorities: `low`, `medium`, `high`, `urgent`

#### Invoice Model
- Belongs to: Project, Client
- Has many: Payments
- Methods: `getRemainingBalanceAttribute()`, `isFullyPaid()`

#### Payment Model
- Belongs to: Invoice

#### Salary Model
- Belongs to: User, Project (optional)

### 3. Database Seeding
Sample data created:

**Roles:**
- Admin (Full system access)
- Team Member (Assigned tasks access)
- Client (Read-only project access)

**Users:**
- admin@asynchronousdigital.com (Admin)
- john@asynchronousdigital.com (Developer)
- sarah@asynchronousdigital.com (Designer)
- client@example.com (Demo Client)

**Teams:**
- Development Team (John assigned)
- Design Team (Sarah assigned)

**Default Password:** `password` (for all users)

### 4. Project Structure
```
asynchronousdigital/
├── app/
│   └── Models/
│       ├── Role.php
│       ├── User.php
│       ├── Team.php
│       ├── Client.php
│       ├── Project.php
│       ├── Task.php
│       ├── Invoice.php
│       ├── Payment.php
│       └── Salary.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_roles_table.php
│   │   ├── 0001_01_01_000001_create_users_table.php
│   │   ├── 2026_01_30_065402_create_teams_table.php
│   │   ├── 2026_01_30_065403_create_team_user_table.php
│   │   ├── 2026_01_30_065404_create_clients_table.php
│   │   ├── 2026_01_30_065405_create_projects_table.php
│   │   ├── 2026_01_30_065406_create_project_team_table.php
│   │   ├── 2026_01_30_065407_create_tasks_table.php
│   │   ├── 2026_01_30_065408_create_task_user_table.php
│   │   ├── 2026_01_30_065409_create_task_team_table.php
│   │   ├── 2026_01_30_065410_create_invoices_table.php
│   │   ├── 2026_01_30_065411_create_payments_table.php
│   │   └── 2026_01_30_065412_create_salaries_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       ├── UserSeeder.php
│       └── TeamSeeder.php
└── public/
    └── landing-page.html (Your existing company website)
```

## 🚀 Next Steps (Phase 2)

### 1. Install AdminLTE
```bash
composer require jeroennoten/laravel-adminlte
php artisan adminlte:install
```

### 2. Create Controllers
```bash
# Dashboard Controllers
php artisan make:controller Admin/DashboardController
php artisan make:controller TeamMember/DashboardController
php artisan make:controller Client/DashboardController

# Module Controllers
php artisan make:controller Admin/ClientController --resource
php artisan make:controller Admin/ProjectController --resource
php artisan make:controller Admin/TaskController --resource
php artisan make:controller Admin/TeamController --resource
php artisan make:controller Admin/InvoiceController --resource
php artisan make:controller Admin/SalaryController --resource
```

### 3. Create Blade Views
- Admin Dashboard (Trello-style task board)
- Team Member Dashboard (Personal task board)
- Client Dashboard (Read-only project view)
- CRUD views for all modules

### 4. Implement Features
- ✅ Authentication middleware per role
- ✅ Trello-style drag-and-drop task board (using Livewire or Vue.js)
- ✅ Task filtering (by project, client, assignee, team)
- ✅ Invoice generation and PDF export
- ✅ Payment tracking
- ✅ Salary calculation and payment history
- ✅ Role-based permissions (using Laravel Policies)

### 5. Public Website Integration
- Create routes for public pages
- Integrate landing-page.html as public home
- Add portfolio management from admin panel
- Create contact form handler

## 🗃️ Database Schema Summary

### Key Relationships
```
Users ↔ Teams (Many-to-Many via team_user)
Projects ↔ Teams (Many-to-Many via project_team)
Tasks ↔ Users (Many-to-Many via task_user)
Tasks ↔ Teams (Many-to-Many via task_team)
Client → User (One-to-One)
Project → Client (One-to-Many)
Task → Project (One-to-Many)
Invoice → Project (One-to-Many)
Invoice → Client (One-to-Many)
Payment → Invoice (One-to-Many)
Salary → User (One-to-Many)
```

### Task Statuses (Trello Columns)
- `to_do` - Not started
- `in_progress` - Currently being worked on
- `review` - Pending review
- `done` - Completed

### Project Billing Models
- `task_based` - Bill per task completion
- `monthly` - Monthly retainer
- `fixed_price` - One-time project fee

### Team Member Payment Models
- `monthly` - Monthly salary
- `project_based` - Per project payment
- `task_based` - Per task payment
- `contractual` - Contract-based

## 📋 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@asynchronousdigital.com | password |
| Team Member | john@asynchronousdigital.com | password |
| Team Member | sarah@asynchronousdigital.com | password |
| Client | client@example.com | password |

## 🔧 Environment Setup

### Database Configuration (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asynchronousdigital
DB_USERNAME=root
DB_PASSWORD=admin
```

### Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

## 📊 Features Overview

### Admin Dashboard
- View all tasks across all projects (Trello board)
- Filter tasks by project, client, assignee, team
- Drag & drop task status updates
- Active projects summary
- Unpaid invoices
- Pending team payments
- Monthly revenue

### Team Member Dashboard
- Personal task board (only assigned tasks)
- Drag & drop own tasks
- Time tracking (optional)
- Tasks due today
- Overdue tasks
- Completed tasks this month

### Client Dashboard
- Read-only project task board
- Project progress tracking
- Invoice history
- Payment status

### Modules
1. **CRM** - Client and project management
2. **Task Management** - Trello-style boards with team/individual assignments
3. **Team Management** - Multi-team support with member assignments
4. **Accounting** - Invoicing, payments, revenue tracking
5. **HR** - Payroll, salary tracking, payment history

## 🎨 Technology Stack
- **Backend:** Laravel 12.x
- **Frontend:** Blade + AdminLTE
- **Database:** MySQL
- **Authentication:** Laravel Breeze/Fortify (to be implemented)
- **UI Framework:** AdminLTE (open source)

## ✨ System Highlights
- ✅ Clean MVC architecture
- ✅ Normalized database with proper foreign keys
- ✅ Soft deletes on major entities
- ✅ Many-to-many relationships with pivot tables
- ✅ Role-based access control ready
- ✅ Modular structure for easy scaling
- ✅ Team-centric design

---

**Ready to build the UI and controllers!** 🚀
