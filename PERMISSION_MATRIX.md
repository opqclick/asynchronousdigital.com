# Permission Matrix

## Purpose
This document defines the permission model for admin capabilities in the application.

- Role permissions are the baseline.
- User overrides (`allow` / `deny`) take priority over role baseline.
- `deny` is effective for all users, including users with admin role.

---

## Permission Resolution Order
For each permission check:

1. Check user override in `permission_user` table.
   - If override exists with `allowed = 1` => allow.
   - If override exists with `allowed = 0` => deny.
2. If no override exists, check permissions inherited from assigned roles.
3. If role grants permission => allow, otherwise deny.

---

## Admin Permission Catalog
These permission keys are recognized and seedable:

- `dashboard.view`
- `projects.manage`
- `tasks.manage`
- `clients.manage`
- `teams.manage`
- `invoices.manage`
- `payments.manage`
- `salaries.manage`
- `users.manage`
- `settings.manage`
- `permissions.manage`
- `user-activities.view`
- `user-activities.edit`
- `user-activities.delete`
- `user-activities.restore`
- `services.manage`
- `team-content.manage`
- `testimonials.manage`
- `contact-messages.manage`
- `recycle-bin.view`
- `recycle-bin.restore`

---

## Route to Permission Matrix (Admin Area)

| Module | Route Prefix / Resource | Required Permission |
|---|---|---|
| Dashboard | `/admin/dashboard` | `dashboard.view` |
| Projects | `/admin/projects/*` | `projects.manage` |
| Tasks | `/admin/tasks/*` + task ajax endpoints | `tasks.manage` |
| Clients | `/admin/clients/*` | `clients.manage` |
| Teams | `/admin/teams/*` | `teams.manage` |
| Invoices | `/admin/invoices/*` | `invoices.manage` |
| Payments | `/admin/payments/*` | `payments.manage` |
| Salaries | `/admin/salaries/*` | `salaries.manage` |
| Users | `/admin/users/*` | `users.manage` |
| User invitation / impersonate | `/admin/users/{user}/send-invitation`, `/admin/users/{user}/impersonate` | `users.manage` |
| Client invitation | `/admin/clients/{client}/send-invitation` | `clients.manage` |
| Settings | `/admin/settings` | `settings.manage` |
| Role permissions | `/admin/permissions/roles/*` | `permissions.manage` |
| User overrides | `/admin/permissions/users/*` | `permissions.manage` |
| User activities list/details | `/admin/user-activities`, `/admin/user-activities/{activity}` | `user-activities.view` |
| User activities edit/update | `/admin/user-activities/{activity}/edit`, `/admin/user-activities/{activity}` | `user-activities.edit` |
| User activities delete | `/admin/user-activities/{activity}` (DELETE) | `user-activities.delete` |
| User activities restore | `/admin/user-activities/{activity}/restore` | `user-activities.restore` |
| Services | `/admin/services/*` | `services.manage` |
| Team Content | `/admin/team-contents/*` | `team-content.manage` |
| Testimonials | `/admin/testimonials/*` | `testimonials.manage` |
| Contact messages | `/admin/contact-messages/*` | `contact-messages.manage` |
| Recycle bin list | `/admin/recycle-bin` | `recycle-bin.view` |
| Recycle bin restore | `/admin/recycle-bin/{type}/{id}/restore` | `recycle-bin.restore` |

> Note: These routes still require admin role middleware where configured; permission middleware is now used to granularly restrict activities per user.

---

## Sidebar Visibility Matrix
Admin sidebar items are permission-gated using the same keys above.
If a user is denied a permission, that module link is hidden and route access is also blocked.

---

## Restricting a Secondary Admin (Recommended Flow)
1. Create the admin user with admin role.
2. Open **Admin > User Overrides**.
3. For the target user, set explicit **Deny** on sensitive permissions (for example `users.manage`, `settings.manage`, `permissions.manage`).
4. Keep required business permissions as baseline (from role) or set explicit **Allow** where needed.
5. Test by logging in as that user.

---

## Seeder / Sync Notes
After adding or renaming permission keys in code:

```bash
php artisan db:seed --class=PermissionSeeder
```

This command:
- creates missing permission records
- syncs role-permission assignments from role defaults

---

## Naming Convention
Use `<module>.<action>` style for consistency.

Examples:
- `projects.manage`
- `user-activities.view`
- `team-content.manage`

Keep names stable once used in production to avoid orphaned overrides.
