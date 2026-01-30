# Git Branching Strategy

This document outlines the branching strategy for the Asynchronous Digital CRM project.

## Branch Structure

### 1. `main` (Production)
- **Purpose**: Production-ready code
- **Protection**: Protected branch, requires pull request reviews
- **Merges From**: `dev` branch only
- **Deployment**: Automatically deploys to production environment
- **Rules**:
  - Never commit directly to `main`
  - Only merge from `dev` after thorough testing
  - All merges must be via Pull Request
  - Requires approval before merging

### 2. `dev` (Development/Integration)
- **Purpose**: Integration branch for all features and bug fixes
- **Protection**: Protected branch, requires pull request reviews
- **Merges From**: Feature branches, bugfix branches, hotfix branches
- **Merges To**: `main` branch
- **Rules**:
  - All feature branches merge here first
  - Must pass all tests before merging to `main`
  - Regular merges to `main` after QA approval

### 3. Feature Branches
- **Naming Convention**: `feature/<feature-name>`
- **Examples**:
  - `feature/user-authentication`
  - `feature/digital-ocean-storage`
  - `feature/project-attachments`
- **Created From**: `dev` branch
- **Merges To**: `dev` branch
- **Lifecycle**: Delete after successful merge

### 4. Bugfix Branches
- **Naming Convention**: `bugfix/<bug-description>`
- **Examples**:
  - `bugfix/fix-attachment-upload`
  - `bugfix/user-profile-display`
- **Created From**: `dev` branch
- **Merges To**: `dev` branch
- **Lifecycle**: Delete after successful merge

### 5. Hotfix Branches
- **Naming Convention**: `hotfix/<issue-description>`
- **Examples**:
  - `hotfix/critical-security-patch`
  - `hotfix/production-bug-fix`
- **Created From**: `main` branch (for production issues)
- **Merges To**: Both `main` AND `dev` branches
- **Lifecycle**: Delete after successful merge to both branches

## Workflow

### Starting a New Feature

```bash
# 1. Switch to dev branch
git checkout dev

# 2. Pull latest changes
git pull origin dev

# 3. Create feature branch
git checkout -b feature/your-feature-name

# 4. Work on your feature, commit regularly
git add .
git commit -m "feat: your feature description"

# 5. Push to remote
git push origin feature/your-feature-name

# 6. Create Pull Request to dev branch on GitHub
# 7. After approval, merge to dev
# 8. Delete feature branch
git branch -d feature/your-feature-name
git push origin --delete feature/your-feature-name
```

### Merging to Production

```bash
# 1. Ensure dev is up to date and tested
git checkout dev
git pull origin dev

# 2. Create Pull Request from dev to main on GitHub
# 3. After approval and successful CI/CD, merge to main
# 4. Tag the release
git checkout main
git pull origin main
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### Handling Hotfixes

```bash
# 1. Create hotfix from main
git checkout main
git pull origin main
git checkout -b hotfix/critical-issue

# 2. Fix the issue and commit
git add .
git commit -m "hotfix: description of fix"

# 3. Push hotfix branch
git push origin hotfix/critical-issue

# 4. Create Pull Request to main
# 5. After merge to main, also merge to dev
git checkout dev
git merge hotfix/critical-issue
git push origin dev

# 6. Delete hotfix branch
git branch -d hotfix/critical-issue
git push origin --delete hotfix/critical-issue
```

## Commit Message Convention

Follow conventional commits format:

- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting, etc.)
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `chore:` Maintenance tasks

**Examples:**
```
feat: add file upload to projects
fix: resolve attachment display issue
docs: update README with setup instructions
refactor: optimize database queries
```

## Pull Request Guidelines

### Required Information
- **Title**: Clear, descriptive title
- **Description**: What changes were made and why
- **Related Issues**: Link to relevant GitHub issues
- **Testing**: How the changes were tested
- **Screenshots**: For UI changes

### Review Process
1. At least 1 approval required for merging to `dev`
2. At least 2 approvals required for merging to `main`
3. All CI/CD checks must pass
4. No merge conflicts

## Branch Protection Rules (GitHub Settings)

### For `main` branch:
- ✅ Require pull request reviews before merging (2 approvals)
- ✅ Require status checks to pass before merging
- ✅ Require branches to be up to date before merging
- ✅ Include administrators in restrictions
- ✅ Restrict who can push to matching branches
- ✅ Require linear history
- ✅ Do not allow bypassing the above settings

### For `dev` branch:
- ✅ Require pull request reviews before merging (1 approval)
- ✅ Require status checks to pass before merging
- ✅ Require branches to be up to date before merging
- ✅ Do not allow force pushes

## CI/CD Pipeline

### On Pull Request to `dev`:
1. Run automated tests
2. Check code style/linting
3. Run security scans
4. Build application
5. Deploy to staging environment (optional)

### On Merge to `main`:
1. Run all tests
2. Build production assets
3. Create release tag
4. Deploy to production
5. Send deployment notifications

## Best Practices

1. **Keep branches short-lived**: Merge feature branches within 1-2 days
2. **Regular pulls**: Pull from `dev` frequently to avoid merge conflicts
3. **Small commits**: Make small, focused commits with clear messages
4. **Test locally**: Always test your changes locally before pushing
5. **Update documentation**: Update docs when adding new features
6. **Code reviews**: Review others' code and learn from feedback
7. **Clean up**: Delete merged branches promptly

## Visual Flow

```
main (production)
  ↑
  │ (Pull Request, reviewed & tested)
  │
dev (integration)
  ↑
  ├─ feature/user-management
  ├─ feature/file-uploads
  ├─ bugfix/login-issue
  └─ feature/reporting

hotfix/critical-bug → main (then merge to dev)
```

## Current Status

- **Current Branch**: `html-only` (needs to be deprecated)
- **To Be Created**: `dev` branch as primary development branch
- **Production Branch**: `main` (already exists)

## Next Steps

1. Create `dev` branch from `main`
2. Commit current Laravel CRM work to `dev`
3. Deprecate `html-only` branch
4. Set up branch protection rules on GitHub
5. Create first feature branch for ongoing work
6. Set up CI/CD pipeline (GitHub Actions)

## Questions?

If you have questions about the branching strategy, please:
1. Check this document first
2. Ask in team chat
3. Consult with team lead
