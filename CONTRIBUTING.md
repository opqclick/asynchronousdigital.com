# Contributing to Asynchronous Digital CRM

Thank you for considering contributing to our project! This guide will help you understand our workflow and requirements.

## 📋 Table of Contents

- [Getting Started](#getting-started)
- [Branching Strategy](#branching-strategy)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Commit Messages](#commit-messages)
- [Pull Request Process](#pull-request-process)
- [Testing](#testing)
- [Code Review](#code-review)

## 🚀 Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL 8.0+
- Git

### Initial Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/opqclick/asynchronousdigital.com.git
   cd asynchronousdigital
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   - Update `.env` with your database credentials
   - Run migrations: `php artisan migrate --seed`

5. **Set up storage**
   ```bash
   php artisan storage:link
   ```

6. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

## 🌿 Branching Strategy

We follow a structured branching model:

```
main (production)
  ↑
  │ Pull Request
  │
dev (integration)
  ↑
  ├─ feature/feature-name
  ├─ bugfix/bug-description
  └─ hotfix/critical-fix
```

### Branch Types

| Branch Type | Prefix | Created From | Merges To | Example |
|------------|--------|--------------|-----------|---------|
| Feature | `feature/` | `dev` | `dev` | `feature/user-notifications` |
| Bug Fix | `bugfix/` | `dev` | `dev` | `bugfix/login-validation` |
| Hotfix | `hotfix/` | `main` | `main` & `dev` | `hotfix/security-patch` |
| Release | `release/` | `dev` | `main` & `dev` | `release/v1.2.0` |

### Branch Naming Convention

Use lowercase with hyphens:
- ✅ `feature/add-payment-gateway`
- ✅ `bugfix/fix-invoice-calculation`
- ❌ `Feature/AddPaymentGateway`
- ❌ `fix_invoice`

## 💻 Development Workflow

### Working on a New Feature

1. **Update your local dev branch**
   ```bash
   git checkout dev
   git pull origin dev
   ```

2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
   - Write clean, documented code
   - Follow PSR-12 coding standards
   - Add tests for new functionality

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: add user notification system"
   ```

5. **Keep your branch updated**
   ```bash
   git fetch origin dev
   git rebase origin/dev
   ```

6. **Push your branch**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Go to GitHub and create a PR to `dev`
   - Fill out the PR template completely
   - Request reviews from team members

### Working on a Bug Fix

Same process as features, but use `bugfix/` prefix:

```bash
git checkout dev
git pull origin dev
git checkout -b bugfix/fix-profile-upload
# ... make changes ...
git commit -m "fix: resolve profile picture upload issue"
git push origin bugfix/fix-profile-upload
```

### Handling Hotfixes (Production Issues)

1. **Create from main**
   ```bash
   git checkout main
   git pull origin main
   git checkout -b hotfix/critical-security-fix
   ```

2. **Fix the issue**
   ```bash
   git commit -m "hotfix: patch security vulnerability"
   ```

3. **Push and create PR to main**
   ```bash
   git push origin hotfix/critical-security-fix
   ```

4. **After merging to main, also merge to dev**
   ```bash
   git checkout dev
   git pull origin dev
   git merge hotfix/critical-security-fix
   git push origin dev
   ```

## 📝 Coding Standards

### PHP Standards

Follow PSR-12 coding standards:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();
        
        return view('items.index', compact('items'));
    }
}
```

### Laravel Best Practices

- Use Eloquent ORM for database queries
- Follow RESTful conventions for routes
- Use form requests for validation
- Keep controllers thin, use services for business logic
- Use dependency injection
- Write descriptive variable and method names

### Blade Templates

```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>
        
        @if ($items->count() > 0)
            @foreach ($items as $item)
                <div>{{ $item->name }}</div>
            @endforeach
        @else
            <p>No items found.</p>
        @endif
    </div>
@endsection
```

### JavaScript/Vue

- Use ES6+ syntax
- Use meaningful variable names
- Comment complex logic
- Avoid global variables

## 📌 Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

| Type | Description | Example |
|------|-------------|---------|
| `feat` | New feature | `feat: add payment processing` |
| `fix` | Bug fix | `fix: resolve login redirect issue` |
| `docs` | Documentation | `docs: update API documentation` |
| `style` | Code style | `style: format code with prettier` |
| `refactor` | Code refactoring | `refactor: simplify user validation` |
| `test` | Tests | `test: add unit tests for auth` |
| `chore` | Maintenance | `chore: update dependencies` |
| `perf` | Performance | `perf: optimize database queries` |

### Examples

```bash
# Simple commit
git commit -m "feat: add email verification"

# With scope
git commit -m "fix(auth): resolve password reset token expiry"

# With body
git commit -m "feat: implement role-based access control

Add middleware to check user roles and permissions.
Update routes to use role middleware.
Add role management views for admins."

# Breaking change
git commit -m "feat!: change API response format

BREAKING CHANGE: API now returns data in camelCase instead of snake_case"
```

## 🔄 Pull Request Process

### Before Creating a PR

1. ✅ Ensure your code passes all tests
2. ✅ Run code style checks
3. ✅ Update documentation
4. ✅ Add/update tests
5. ✅ Resolve any merge conflicts
6. ✅ Rebase on latest dev branch

### Creating a PR

1. **Go to GitHub** and click "New Pull Request"
2. **Select branches**: `base: dev` ← `compare: feature/your-branch`
3. **Fill out the PR template**:
   - Clear description of changes
   - Link related issues
   - List all changes made
   - Add screenshots for UI changes
   - Complete testing checklist
4. **Request reviewers**
5. **Add appropriate labels** (bug, enhancement, documentation, etc.)

### PR Requirements

#### For merging to `dev`:
- ✅ At least 1 approval required
- ✅ All CI/CD checks must pass
- ✅ No merge conflicts
- ✅ Branch must be up to date with dev

#### For merging to `main`:
- ✅ At least 2 approvals required
- ✅ All CI/CD checks must pass
- ✅ QA testing completed
- ✅ Documentation updated
- ✅ No merge conflicts

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/UserTest.php

# Run with coverage
php artisan test --coverage

# Run feature tests only
php artisan test --testsuite=Feature

# Run unit tests only
php artisan test --testsuite=Unit
```

### Writing Tests

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
```

### Test Coverage

- Aim for at least 70% code coverage
- All new features must have tests
- Critical paths should have 100% coverage

## 👀 Code Review

### As a Reviewer

- Review within 24 hours if possible
- Be constructive and respectful
- Test the changes locally if needed
- Check for:
  - Code quality and standards
  - Security vulnerabilities
  - Performance implications
  - Test coverage
  - Documentation

### Review Comments

- ✅ "Consider extracting this into a separate method for better readability"
- ✅ "Good implementation! Suggestion: we could cache this query"
- ❌ "This is wrong"
- ❌ "Why did you do it this way?"

### As an Author

- Respond to all comments
- Be open to feedback
- Make requested changes promptly
- Re-request review after changes
- Don't take feedback personally

## 🚫 What NOT to Do

- ❌ Don't commit directly to `main` or `dev`
- ❌ Don't push without testing locally
- ❌ Don't commit secrets or credentials
- ❌ Don't commit commented-out code
- ❌ Don't commit vendor/ or node_modules/
- ❌ Don't force push to shared branches
- ❌ Don't merge your own PRs without approval
- ❌ Don't ignore CI/CD failures

## 🆘 Getting Help

- Check documentation first
- Search existing issues
- Ask in team chat
- Create an issue for bugs
- Tag relevant team members

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHP The Right Way](https://phptherightway.com/)
- [PSR-12 Coding Standards](https://www.php-fig.org/psr/psr-12/)
- [Git Best Practices](https://git-scm.com/book/en/v2)

---

Thank you for contributing! 🎉
