# Git Workflow Quick Reference

Quick commands for common git workflows in this project.

## 🌟 Starting New Work

### Create Feature Branch
```bash
git checkout dev
git pull origin dev
git checkout -b feature/your-feature-name
```

### Create Bug Fix Branch
```bash
git checkout dev
git pull origin dev
git checkout -b bugfix/bug-description
```

### Create Hotfix (Production Bug)
```bash
git checkout main
git pull origin main
git checkout -b hotfix/critical-fix
```

## 💾 Daily Work

### Check Status
```bash
git status
git branch  # see current branch
```

### Stage and Commit
```bash
git add .
git commit -m "feat: your feature description"
```

### Push to Remote
```bash
git push origin your-branch-name
```

## 🔄 Keeping Up to Date

### Update Feature Branch from Dev
```bash
git checkout your-branch
git fetch origin dev
git rebase origin/dev
# or if you prefer merge:
git merge origin/dev
```

### Sync Dev Branch
```bash
git checkout dev
git pull origin dev
```

## 📋 Pull Request Flow

### 1. Push Your Branch
```bash
git push origin feature/your-feature
```

### 2. Create PR on GitHub
- Go to: https://github.com/opqclick/asynchronousdigital.com
- Click "New Pull Request"
- Set base: `dev`, compare: `your-branch`
- Fill out PR template
- Request reviews

### 3. Make Changes from Review
```bash
# Make changes
git add .
git commit -m "refactor: address review comments"
git push origin feature/your-feature
```

### 4. After Merge - Clean Up
```bash
git checkout dev
git pull origin dev
git branch -d feature/your-feature  # delete local
git push origin --delete feature/your-feature  # delete remote
```

## 🔥 Hotfix Flow

```bash
# 1. Create hotfix from main
git checkout main
git pull origin main
git checkout -b hotfix/critical-issue

# 2. Fix and commit
git add .
git commit -m "hotfix: fix critical bug"

# 3. Push and create PR to main
git push origin hotfix/critical-issue
# Create PR on GitHub: main ← hotfix/critical-issue

# 4. After merge to main, merge to dev too
git checkout dev
git pull origin dev
git merge hotfix/critical-issue
git push origin dev

# 5. Clean up
git branch -d hotfix/critical-issue
git push origin --delete hotfix/critical-issue
```

## 🛠️ Useful Commands

### View Commit History
```bash
git log --oneline -10  # last 10 commits
git log --graph --oneline --all  # visual tree
```

### See Changes
```bash
git diff  # unstaged changes
git diff --staged  # staged changes
git diff dev..your-branch  # compare branches
```

### Undo Changes
```bash
git restore file.php  # discard unstaged changes
git restore --staged file.php  # unstage file
git reset HEAD~1  # undo last commit (keep changes)
git reset --hard HEAD~1  # undo last commit (discard changes)
```

### Stash Work
```bash
git stash  # save current work
git stash list  # see stashed work
git stash pop  # restore and remove from stash
git stash apply  # restore but keep in stash
```

### Branch Management
```bash
git branch  # list local branches
git branch -a  # list all branches (including remote)
git branch -d branch-name  # delete local branch
git push origin --delete branch-name  # delete remote branch
```

## 📊 Branch Overview

```
Your Branches:
├── main (production)
│   └── Always stable, deployed to production
│
├── dev (integration)
│   └── Integration branch for all development
│
├── feature/user-auth
│   └── Working on user authentication
│
├── feature/payment-system
│   └── Working on payment integration
│
└── bugfix/login-redirect
    └── Fixing login redirect issue
```

## 🎯 Commit Message Format

```bash
# Feature
git commit -m "feat: add user authentication"
git commit -m "feat(auth): implement JWT tokens"

# Bug Fix
git commit -m "fix: resolve login redirect loop"
git commit -m "fix(payment): handle failed transactions"

# Documentation
git commit -m "docs: update API documentation"

# Style/Formatting
git commit -m "style: format code with PSR-12"

# Refactoring
git commit -m "refactor: simplify user service"

# Tests
git commit -m "test: add unit tests for auth"

# Performance
git commit -m "perf: optimize database queries"

# Chore/Maintenance
git commit -m "chore: update dependencies"
```

## 🚨 Emergency Fixes

### Revert a Commit
```bash
git revert commit-hash  # creates new commit that undoes changes
```

### Force Push (Use Carefully!)
```bash
# Only on your feature branches, never on main or dev!
git push --force-with-lease origin your-branch
```

## ✅ Pre-Push Checklist

Before pushing your code:

- [ ] `git status` - Check what you're committing
- [ ] `php artisan test` - Run all tests
- [ ] `composer install` - Ensure dependencies are updated
- [ ] `php artisan migrate` - Test migrations work
- [ ] `git pull origin dev` - Get latest changes
- [ ] Resolve any conflicts
- [ ] Commit with proper message format

## 🔗 Quick Links

- **Repository**: https://github.com/opqclick/asynchronousdigital.com
- **Create PR**: https://github.com/opqclick/asynchronousdigital.com/pulls
- **Issues**: https://github.com/opqclick/asynchronousdigital.com/issues
- **Branching Strategy**: See `BRANCHING_STRATEGY.md`
- **Contributing Guide**: See `CONTRIBUTING.md`

## 📞 Need Help?

```bash
# See git help
git help
git help commit
git help rebase

# See your git config
git config --list

# See remote URLs
git remote -v
```

---

**Remember**: 
- Never commit directly to `main` or `dev`
- Always create a PR for code review
- Keep commits small and focused
- Write meaningful commit messages
