# Language Selection Feature

## Description
Implemented an intermediate step for selecting native language (`native_lang`) before creating a user in the database.

## Changes

### 1. New Livewire Component: `LanguageSelection`
**Files:**
- `app/Livewire/Auth/LanguageSelection.php`
- `resources/views/livewire/auth/language-selection.blade.php`

**Functionality:**
- Checks for registration data in session
- Displays select with available languages (English/Vietnamese)
- Sets default language based on current locale
- Automatically determines `learning_lang` (opposite of `native_lang`)
- Creates user only after language selection
- Sends welcome notification
- Logs in user and redirects to dashboard

### 2. Updated `AuthController`
**File:** `app/Http/Controllers/Auth/AuthController.php`

**Changes in `register()` method:**
- Removed user creation immediately after registration
- Data saved to session: `name`, `email`, `password`, `timezone`
- Redirect to language selection page: `route('auth.language')`

### 3. Updated `SocialAuthController` (Facebook)
**File:** `app/Http/Controllers/Auth/SocialAuthController.php`

**Changes in `handleFacebookCallback()` method:**
- For new users: save data to session and redirect to language selection
- For existing users: direct login and send welcome notification

### 4. Updated `ZaloController`
**File:** `app/Http/Controllers/Auth/ZaloController.php`

**Changes in `callback()` method:**
- For new users: save data to session and redirect to language selection
- For existing users: direct login and send welcome notification
- Removed hardcoded `native_lang` value when creating user

### 5. Routing
**File:** `routes/web.php`

**Added route:**
```php
Route::get('/auth/language', LanguageSelection::class)->name('auth.language');
```

### 6. Translations
**Files:**
- `resources/lang/en/auth.php`
- `resources/lang/vi/auth.php`

**Added `language_selection` section:**
- `title` - page title
- `description` - description
- `native_lang_label` - select label
- `select_language` - placeholder
- `submit` - button text

## Registration Process

### Regular Registration:
1. User fills form at `/register`
2. Data saved to session
3. Redirect to `/auth/language`
4. User selects `native_lang`
5. Database record created with `native_lang` and automatic `learning_lang`
6. Login and redirect to `/dashboard`

### Social Authorization (Facebook/Zalo):
1. Callback from social network
2. Check if user exists
3. **New user:**
   - Save data to session
   - Redirect to `/auth/language`
   - Language selection
   - User creation
   - Login and redirect
4. **Existing user:**
   - Direct login
   - Welcome notification
   - Redirect to `/dashboard`

## Session Data Structure

```php
session()->get('registration_data') = [
    'name' => string,
    'email' => string,
    'password' => string, // plain text, hashed on creation
    'timezone' => string,
    // for social authorization:
    'facebook_id' => string|null,
    'zalo_user_id' => string|null,
    'avatar' => string|null,
    'email_verified_at' => Carbon|null,
]
```

## learning_lang Determination Logic

```php
$learning_lang = $native_lang === 'en' ? 'vi' : 'en';
```

## Security

- Check for session data before user creation
- Selected language validation via Enum
- Session cleanup after successful registration
- Password hashing before saving

## Notifications

Uses `RegistrationNotification` with personalized greeting:
- Time of day determined based on timezone
- Greeting selected in chosen language
- Sent through Laravel notification system
