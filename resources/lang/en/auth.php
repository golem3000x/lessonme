<?php

declare(strict_types=1);

return [
    // Login page
    'login' => [
        'title' => 'Sign in to your account',
        'or_register' => 'Or',
        'register_link' => 'create an account',
        'email_label' => 'Email address',
        'email_placeholder' => 'Email address',
        'password_label' => 'Password',
        'password_placeholder' => 'Password',
        'remember_me' => 'Remember me',
        'submit' => 'Sign in',
        'or_continue_with' => 'Or continue with',
        'sign_in_with_zalo' => 'Sign in with Zalo',
        'sign_in_with_facebook' => 'Sign in with Facebook',
    ],

    // Register page
    'register' => [
        'title' => 'Create an account',
        'or_login' => 'Or',
        'login_link' => 'sign in to your account',
        'name_label' => 'Name',
        'name_placeholder' => 'Your name',
        'email_label' => 'Email address',
        'email_placeholder' => 'Email address',
        'password_label' => 'Password',
        'password_placeholder' => 'Minimum 8 characters',
        'password_confirmation_label' => 'Confirm password',
        'password_confirmation_placeholder' => 'Repeat password',
        'submit' => 'Create account',
    ],

    // Language selection page
    'language_selection' => [
        'title' => 'Choose Your Native Language',
        'description' => 'Please select your native language to personalize your experience.',
        'native_lang_label' => 'Native Language',
        'select_language' => 'Select a language...',
        'submit' => 'Complete Registration',
        'info_text' => 'You can change your language preference anytime in your profile settings.',
    ],

    // Common
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Greetings by time of day
    'greetings' => [
        'morning' => 'Good morning',
        'afternoon' => 'Good afternoon',
        'evening' => 'Good evening',
        'night' => 'Good night',
    ],

    // Welcome messages
    'welcome_message' => ':greeting, :name! Welcome back!',
    'registration_message' => ':greeting, :name! Welcome to our platform!',
];
