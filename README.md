# Asynchronous Digital CRM

A comprehensive Customer Relationship Management system built with Laravel 12, featuring project management, team collaboration, and client communication tools.

## Features

### 🎯 Core Modules

- **User Management** - Role-based access control (Admin, Team Member, Client)
- **Project Management** - Complete project lifecycle management with milestones
- **Task Management** - Kanban-style task board with assignments and tracking
- **Contact Messages** - Centralized inbox for client inquiries
- **Services & Portfolio** - Showcase company services and completed projects
- **Testimonials** - Client feedback and reviews management

### 👥 User Roles & Permissions

#### Admin
- Full system access and configuration
- User management and role assignments
- Project and task oversight
- Client communication management
- Service and testimonial management

#### Team Member
- Personal dashboard with assigned tasks
- Kanban board for task management
- Project collaboration
- Time tracking and progress updates

#### Client
- Project visibility and progress tracking
- Document access and downloads
- Task viewing and status updates
- Direct communication with team

### 📊 Dashboard Features

- **Admin Dashboard** - System-wide statistics, user activity, project overview
- **Team Member Dashboard** - Personal task board, due dates, completion metrics
- **Client Dashboard** - Project status, assigned tasks, document access

### 🎨 Public Website

- Modern single-page application design
- Service offerings showcase
- Portfolio with case studies
- Team member profiles
- Contact form with inquiry management
- Testimonials and client reviews

## Tech Stack

- **Framework**: Laravel 12.49.0
- **PHP**: 8.3.27
- **Database**: MySQL
- **Admin UI**: AdminLTE 3.x
- **Frontend**: Blade Templates, Tailwind CSS
- **File Storage**: Digital Ocean Spaces
- **Authentication**: Laravel Breeze

## Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd asynchronousdigital
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** (in `.env`)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asynchronousdigital
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Configure Digital Ocean Spaces** (in `.env`)
```
FILESYSTEM_DISK=do_spaces
DO_SPACES_KEY=your_key
DO_SPACES_SECRET=your_secret
DO_SPACES_ENDPOINT=https://sgp1.digitaloceanspaces.com
DO_SPACES_REGION=sgp1
DO_SPACES_BUCKET=your_bucket_name
DO_SPACES_URL=https://your_bucket_name.sgp1.digitaloceanspaces.com
DO_SPACES_VISIBILITY=public
```

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Build assets**
```bash
npm run build
```

8. **Start the development server**
```bash
php artisan serve
```

## Default Credentials

After seeding, you can login with:

**Admin Account**
- Email: admin@asynchronousdigital.com
- Password: password

**Team Member Account**
- Email: john@asynchronousdigital.com
- Password: password

**Client Account**
- Email: client@example.com
- Password: password

## Git Workflow

This project follows a structured branching strategy:

- **main** - Production-ready code
- **dev** - Integration branch for development
- **feature/** - New features
- **bugfix/** - Bug fixes
- **hotfix/** - Critical production fixes

For detailed workflow instructions, see:
- [BRANCHING_STRATEGY.md](BRANCHING_STRATEGY.md)
- [CONTRIBUTING.md](CONTRIBUTING.md)
- [GIT_WORKFLOW.md](GIT_WORKFLOW.md)

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/          # Admin panel controllers
│   ├── TeamMember/     # Team member controllers
│   ├── Client/         # Client portal controllers
│   └── PublicController.php
├── Models/             # Eloquent models
└── Providers/

database/
├── migrations/         # Database schema
└── seeders/           # Sample data

resources/
├── views/
│   ├── admin/         # Admin panel views
│   ├── team-member/   # Team member views
│   ├── client/        # Client portal views
│   ├── public/        # Public website
│   └── components/    # Reusable components
└── css/

routes/
└── web.php            # Application routes

public/
├── css/
│   └── admin-custom.css  # AdminLTE customizations
└── logo.png           # Company logo
```

## Database Schema

### Core Tables
- `users` - User accounts with role relationships
- `roles` - User roles and permissions
- `projects` - Project management
- `tasks` - Task management with pivot table for assignments
- `services` - Service offerings
- `portfolios` - Portfolio showcase
- `testimonials` - Client reviews
- `contact_messages` - Client inquiries
- `team_members` - Team profiles

## File Storage

All file uploads (profile pictures, documents, project attachments) are stored in Digital Ocean Spaces with public visibility. Files are organized in the following structure:

```
AsynchronousDigitalCRM/
├── profile_pictures/
├── user_documents/
├── projects/
└── tasks/
```

## Custom Features

- **Kanban Task Board** - Drag-and-drop task management (To Do, In Progress, Review, Done)
- **Role-based Dashboards** - Customized views for each user role
- **File Management** - Secure file uploads with Digital Ocean Spaces integration
- **Activity Tracking** - Comprehensive audit trail for projects and tasks
- **Responsive Design** - Mobile-friendly interface across all modules
- **Custom Logo Implementation** - Branded frontend and backend interfaces

## Development

### Clear Cache
```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear
```

### Run Tests
```bash
php artisan test
```

### Code Style
This project follows PSR-12 coding standards and uses conventional commits format.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is proprietary software. All rights reserved.

## Support

For support, email support@asynchronousdigital.com or create an issue in the repository.
