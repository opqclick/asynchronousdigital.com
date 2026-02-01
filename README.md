# Asynchronous Digital CRM

A comprehensive Customer Relationship Management system built with Laravel 12, featuring project management, team collaboration, client communication, and automated user invitation system.

## Features

### 🎯 Core Modules

- **User Management** - Role-based access control (Admin, Team Member, Client)
- **User Invitation System** - Automated email invitations with login credentials
- **Project Management** - Complete project lifecycle management with milestones
- **Task Management** - Kanban-style task board with assignments and tracking
- **Contact Messages** - Centralized inbox for client inquiries
- **Services & Portfolio** - Showcase company services and completed projects
- **Testimonials** - Client feedback and reviews management

### 📧 Email Features

- **SMTP Integration** - Gmail SMTP for production emails
- **User Invitations** - Automatic welcome emails with credentials
- **Environment Indicators** - Email subjects show [LOCAL], [DEV], [STAGING] prefix for non-production
- **Queue Support** - Background email processing via database queue
- **Resend Invitations** - One-click resend from user/client lists

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

6. **Configure mail settings** (in `.env`)
```env
# For development (log emails to storage/logs)
MAIL_MAILER=log

# For production (send via SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-gmail-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> **Note:** For Gmail, you need to generate an [App Password](https://myaccount.google.com/apppasswords)

7. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

8. **Build assets**
```bash
npm run build
```

9. **Start the development server**
```bash
php artisan serve
```

## Production Deployment

### Manual Steps Required on Production

1. **Set environment to production** (`.env`)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
LOG_LEVEL=error
SESSION_ENCRYPT=true
TELESCOPE_ENABLED=false
```

2. **Configure queue worker** (for email processing)
```bash
# Run queue worker (use supervisor or systemd for persistent process)
php artisan queue:work --daemon --tries=3

# Or for production with supervisor:
# Install supervisor and create config:
sudo apt-get install supervisor

# Add to /etc/supervisor/conf.d/laravel-worker.conf:
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasup=unexpected
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600

# Start supervisor:
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

3. **Set up scheduled tasks** (crontab)
```bash
crontab -e

# Add this line:
* * * * * cd /path/to/your/app && php artisan schedule:run >> /dev/null 2>&1
```

4. **Clear and cache configs**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. **Set proper permissions**
```bash
sudo chown -R www-data:www-data /path/to/your/app
sudo chmod -R 775 /path/to/your/app/storage
sudo chmod -R 775 /path/to/your/app/bootstrap/cache
```

6. **Run migrations** (if not already done)
```bash
php artisan migrate --force
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminUserSeeder
```

## Default Credentials

After seeding, you can login with:

**Admin Account**
- Email: admin@asynchronousdigital.com
- Password: password

> ⚠️ **Important:** Change the default admin password immediately after first login!

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

- **User Invitation System** - Automated email invitations when creating users/clients
- **Resend Invitations** - One-click invitation resend with new temporary password
- **Environment-aware Emails** - Email subjects show environment (LOCAL/DEV/STAGING)
- **Kanban Task Board** - Drag-and-drop task management (To Do, In Progress, Review, Done)
- **Role-based Dashboards** - Customized views for each user role
- **File Management** - Secure file uploads with Digital Ocean Spaces integration
- **Activity Tracking** - Comprehensive audit trail for projects and tasks
- **Responsive Design** - Mobile-friendly interface across all modules
- **Custom Logo Implementation** - Branded frontend and backend interfaces

## Email Configuration

### Development
Set `QUEUE_CONNECTION=sync` in `.env` for immediate email sending (emails logged to `storage/logs/laravel.log`)

### Production
1. Set `QUEUE_CONNECTION=database` in `.env`
2. Configure SMTP credentials (Gmail app password recommended)
3. Run queue worker: `php artisan queue:work --daemon`
4. Set up supervisor to keep queue worker running (see Production Deployment section)

For detailed email system documentation, see [USER_INVITATION_SYSTEM.md](USER_INVITATION_SYSTEM.md)

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
