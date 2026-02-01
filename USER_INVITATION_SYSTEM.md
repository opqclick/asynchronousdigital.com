# User Invitation System

## Overview
When an admin creates a new user through the admin panel, an invitation email is automatically sent to the user with their login credentials and a welcome message.

## Features Implemented

### 1. Mailable Class: `UserInvitation`
- **Location:** `app/Mail/UserInvitation.php`
- **Purpose:** Handles sending invitation emails to newly created users
- **Queue Support:** Implements `ShouldQueue` for asynchronous email sending
- **Data Passed:**
  - User's name
  - User's email
  - Plain text password
  - Login URL

### 2. Email Template
- **Location:** `resources/views/emails/user-invitation.blade.php`
- **Design:** Professional, responsive email with gradient header and clear call-to-action
- **Content Includes:**
  - Welcome message
  - Login credentials (email and password)
  - Security note about changing password
  - Login button/link
  - Support contact information

### 3. Controller Integration
- **Updated:** `app/Http/Controllers/Admin/UserController@store()`
- **Changes:**
  - Stores plain password before hashing for email
  - Sends invitation email after successful user creation
  - Includes error handling (logs errors without failing user creation)
  - Updated success message to indicate email was sent

## How It Works

1. **Admin Creates User:**
   - Admin fills out user creation form at `/admin/users/create`
   - Enters all required information including password

2. **User Creation Process:**
   ```php
   // Password is stored in plain text temporarily
   $plainPassword = $validated['password'];
   
   // User is created with hashed password
   $user = User::create([...]);
   
   // Invitation email is sent
   Mail::to($user->email)->send(new UserInvitation($user, $plainPassword));
   ```

3. **Email Delivery:**
   - Email is queued for delivery (background processing)
   - User receives professional invitation email
   - Email includes login credentials and direct link to login page

4. **User Login:**
   - User clicks "Login to Your Account" button
   - Uses provided credentials to login
   - Should change password after first login (security best practice)

## Mail Configuration

### Development Environment
- **Driver:** `log` (emails are logged to `storage/logs/laravel.log`)
- **From Address:** `asynchronousd@gmail.com`
- **From Name:** `${APP_NAME}` (Asynchronous Digital)

### Production Environment
To enable actual email sending in production, update `.env`:

```env
# For SMTP (e.g., Gmail, Mailgun, SendGrid)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="asynchronousd@gmail.com"
MAIL_FROM_NAME="Asynchronous Digital"
```

### Queue Configuration
The invitation email implements `ShouldQueue`, which requires:

```env
QUEUE_CONNECTION=database  # Already configured in production .env
```

Run queue worker in production:
```bash
php artisan queue:work
```

Or use supervisor for persistent queue processing:
```bash
sudo supervisorctl start laravel-worker:*
```

## Security Considerations

1. **Password in Email:**
   - Plain text password is sent only once via email
   - User is advised to change password after first login
   - Password is immediately hashed before storing in database

2. **Email Logging:**
   - In development, emails are logged (may contain passwords)
   - Ensure `storage/logs/` is in `.gitignore`
   - Rotate logs regularly in production

3. **Queue Jobs:**
   - Failed jobs are logged to `failed_jobs` table
   - Monitor failed jobs regularly: `php artisan queue:failed`

## Testing the Feature

### In Development:
1. Create a new user through admin panel
2. Check `storage/logs/laravel.log` for email content
3. Verify all credentials are present in the logged email

### In Production:
1. Ensure mail configuration is correct
2. Create a test user with your own email
3. Verify you receive the invitation email
4. Test the login link and credentials
5. Verify password change functionality works

## Customization

### Change Email Design:
Edit `resources/views/emails/user-invitation.blade.php`

### Change Email Content:
Update the text, add more information, or modify the greeting

### Change Subject Line:
Edit `app/Mail/UserInvitation.php`:
```php
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Your Custom Subject Line',
    );
}
```

### Add Attachments:
Update the `attachments()` method in `UserInvitation.php`

## Error Handling

The system includes error handling to ensure user creation doesn't fail if email sending fails:

```php
try {
    Mail::to($user->email)->send(new UserInvitation($user, $plainPassword));
} catch (\Exception $e) {
    \Log::error('Failed to send invitation email: ' . $e->getMessage());
}
```

- User is still created even if email fails
- Error is logged for admin review
- Admin can manually send credentials if needed

## Monitoring

### Check Mail Logs:
```bash
tail -f storage/logs/laravel.log | grep "invitation"
```

### Check Queue Status:
```bash
php artisan queue:listen --verbose
```

### Check Failed Jobs:
```bash
php artisan queue:failed
```

## Future Enhancements

Potential improvements:
1. **Password Reset Token:** Instead of sending password, send a "Set Password" link
2. **Email Verification:** Require email verification before first login
3. **Invitation Expiry:** Add expiration time for invitation links
4. **Resend Invitation:** Add button to resend invitation from user list
5. **Email Templates:** Add multiple email templates for different user roles
6. **Notification Preferences:** Allow users to opt-out of certain notifications

## Support

For issues or questions:
- Email: asynchronousd@gmail.com
- Check logs: `storage/logs/laravel.log`
- Review failed jobs: `php artisan queue:failed`
