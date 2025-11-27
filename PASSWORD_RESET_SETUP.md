# Password Reset Setup Instructions

## Overview
The password reset functionality has been successfully implemented. Users can now reset their passwords using email links.

## Features Added

### 1. Routes
- `GET /forgot-password` - Display forgot password form
- `POST /forgot-password` - Send reset link email
- `GET /reset-password/{token}` - Display reset password form
- `POST /reset-password` - Process password reset

### 2. Views
- `/resources/views/auth/forgot-password.blade.php` - Forgot password form
- `/resources/views/auth/reset-password.blade.php` - Reset password form
- Updated `/resources/views/auth/login.blade.php` with "Forgot password?" link

### 3. Controller
- `/app/Http/Controllers/Auth/PasswordResetController.php` - Handles password reset logic

### 4. Translations
Added translations in both English and Vietnamese:
- `auth.login.forgot_password` - "Forgot password?" link text
- `auth.forgot_password.*` - Forgot password page translations
- `auth.reset_password.*` - Reset password page translations
- `auth.reset_link_sent` - Success message
- `auth.reset_success` - Password reset success message
- `auth.reset_token_invalid` - Invalid token error message

## Email Configuration

For the password reset to work, you need to configure email settings in `.env`:

### Option 1: Using Mailtrap (for development)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 2: Using Gmail (for production)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_specific_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 3: Using Mailgun, SendGrid, or other providers
Follow their specific SMTP configuration instructions.

## Testing

1. Navigate to `/login`
2. Click on "Forgot password?" link
3. Enter your email address
4. Click "Email password reset link"
5. Check your email for the reset link
6. Click the link and enter a new password
7. Submit and you'll be redirected to login

## Database

The `password_reset_tokens` table is already created in the database migration:
- `email` (primary key)
- `token` (hashed token)
- `created_at` (expiration tracking)

Tokens expire after 60 minutes by default (configurable in `config/auth.php`).

## Security Features

- Tokens are hashed before storage
- Tokens expire after a configurable time period
- Email validation before sending reset link
- Password confirmation required when resetting
- Remember token regenerated after password reset

## URL Structure

- Forgot Password: `https://yourdomain.com/forgot-password`
- Reset Password: `https://yourdomain.com/reset-password/{token}?email={email}`

## Customization

To customize the password reset email template, publish the Laravel notification views:

```bash
docker compose exec laravel.test php artisan vendor:publish --tag=laravel-notifications
```

Then edit the template at:
`/resources/views/vendor/notifications/email.blade.php`
