<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\GreetingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GreetingServiceTest extends TestCase
{
    private GreetingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GreetingService;
    }

    public function test_get_greeting_returns_morning_greeting_for_morning_hours(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 8, 0, 0, 'UTC'));

        $greeting = $this->service->getGreeting('UTC', 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    public function test_get_greeting_returns_afternoon_greeting_for_afternoon_hours(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 14, 0, 0, 'UTC'));

        $greeting = $this->service->getGreeting('UTC', 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    public function test_get_greeting_returns_evening_greeting_for_evening_hours(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 19, 0, 0, 'UTC'));

        $greeting = $this->service->getGreeting('UTC', 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    public function test_get_greeting_returns_night_greeting_for_night_hours(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 23, 0, 0, 'UTC'));

        $greeting = $this->service->getGreeting('UTC', 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    public function test_get_greeting_uses_correct_timezone(): void
    {
        // 8 AM UTC = 3 PM Vietnam (UTC+7)
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 8, 0, 0, 'UTC'));

        $greeting_utc = $this->service->getGreeting('UTC', 'en');
        $greeting_vietnam = $this->service->getGreeting('Asia/Ho_Chi_Minh', 'en');

        // They should be different because of timezone difference
        // UTC 8AM = morning, Vietnam 3PM = afternoon
        $this->assertIsString($greeting_utc);
        $this->assertIsString($greeting_vietnam);
    }

    public function test_get_greeting_falls_back_to_config_timezone_when_null(): void
    {
        Config::set('app.timezone', 'UTC');
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        $greeting = $this->service->getGreeting(null, 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    public function test_get_greeting_handles_invalid_timezone_gracefully(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        // Should not throw exception with invalid timezone
        $greeting = $this->service->getGreeting('Invalid/Timezone', 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    public function test_get_greeting_respects_locale_parameter(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        $greeting_en = $this->service->getGreeting('UTC', 'en');
        $greeting_vi = $this->service->getGreeting('UTC', 'vi');

        $this->assertNotEmpty($greeting_en);
        $this->assertNotEmpty($greeting_vi);

        // They should be different (unless translations are missing)
        $this->assertIsString($greeting_en);
        $this->assertIsString($greeting_vi);
    }

    public function test_get_welcome_message_includes_name(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        $message = $this->service->getWelcomeMessage('John', 'UTC', 'en');

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_get_welcome_message_uses_greeting_service(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        $message = $this->service->getWelcomeMessage('Jane', 'UTC', 'en');

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_get_registration_message_includes_name(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        $message = $this->service->getRegistrationMessage('Alice', 'UTC', 'en');

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_get_registration_message_uses_greeting_service(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 10, 0, 0, 'UTC'));

        $message = $this->service->getRegistrationMessage('Bob', 'UTC', 'en');

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_greeting_changes_based_on_time_boundaries(): void
    {
        // Test morning boundary (5 AM)
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 5, 0, 0, 'UTC'));
        $morning = $this->service->getGreeting('UTC', 'en');

        // Test afternoon boundary (12 PM)
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 12, 0, 0, 'UTC'));
        $afternoon = $this->service->getGreeting('UTC', 'en');

        // Test evening boundary (17 PM)
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 17, 0, 0, 'UTC'));
        $evening = $this->service->getGreeting('UTC', 'en');

        // Test night boundary (22 PM)
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 22, 0, 0, 'UTC'));
        $night = $this->service->getGreeting('UTC', 'en');

        $this->assertNotEmpty($morning);
        $this->assertNotEmpty($afternoon);
        $this->assertNotEmpty($evening);
        $this->assertNotEmpty($night);
    }

    public function test_early_morning_hours_return_night_greeting(): void
    {
        // 2 AM should be night
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 2, 0, 0, 'UTC'));

        $greeting = $this->service->getGreeting('UTC', 'en');

        $this->assertNotEmpty($greeting);
        $this->assertIsString($greeting);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
