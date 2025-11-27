<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;

class GreetingService
{
    /**
     * Get greeting based on time of day
     */
    public function getGreeting(?string $timezone = null, string $locale = 'en'): string
    {
        $timezone = $timezone ?? config('app.timezone');

        try {
            $hour = Carbon::now($timezone)->hour;
        } catch (\Exception $e) {
            $hour = Carbon::now()->hour;
        }

        $greeting_key = match (true) {
            $hour >= 5 && $hour < 12 => 'auth.greetings.morning',
            $hour >= 12 && $hour < 17 => 'auth.greetings.afternoon',
            $hour >= 17 && $hour < 22 => 'auth.greetings.evening',
            default => 'auth.greetings.night',
        };

        return __($greeting_key, [], $locale);
    }

    /**
     * Get welcome message for notification
     */
    public function getWelcomeMessage(string $name, ?string $timezone = null, string $locale = 'en'): string
    {
        $greeting = $this->getGreeting($timezone, $locale);

        return __('auth.welcome_message', [
            'greeting' => $greeting,
            'name' => $name,
        ], $locale);
    }

    /**
     * Get message for new registration
     */
    public function getRegistrationMessage(string $name, ?string $timezone = null, string $locale = 'en'): string
    {
        $greeting = $this->getGreeting($timezone, $locale);

        return __('auth.registration_message', [
            'greeting' => $greeting,
            'name' => $name,
        ], $locale);
    }
}
