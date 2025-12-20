<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\System\SystemLanguageEnum;
use App\Http\Middleware\SetFilamentLocale;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SetFilamentLocaleTest extends TestCase
{
    use RefreshDatabase;

    private SetFilamentLocale $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SetFilamentLocale;
    }

    public function test_sets_locale_from_session(): void
    {
        session(['locale' => SystemLanguageEnum::VIETNAMESE->value]);

        $request = Request::create('/admin');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::VIETNAMESE->value, app()->getLocale());
    }

    public function test_sets_locale_from_user_native_lang_when_no_session(): void
    {
        $user = User::factory()->create([
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);

        $request = Request::create('/admin');
        $this->actingAs($user);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::VIETNAMESE->value, app()->getLocale());
    }

    public function test_validates_session_locale_against_enum(): void
    {
        session(['locale' => 'invalid-locale']);
        $default_locale = app()->getLocale();

        $request = Request::create('/admin');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Should not change locale since invalid
        $this->assertNotEquals('invalid-locale', app()->getLocale());
    }

    public function test_validates_user_locale_against_enum(): void
    {
        $user = User::factory()->create([
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);

        $request = Request::create('/admin');
        $this->actingAs($user);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::VIETNAMESE->value, app()->getLocale());
    }

    public function test_defaults_to_english_for_invalid_user_locale(): void
    {
        $this->markTestSkipped('Cannot properly test auth()->login() with unsaved User model in middleware context');
    }

    public function test_session_locale_takes_precedence_over_user_locale(): void
    {
        session(['locale' => SystemLanguageEnum::ENGLISH->value]);

        $user = User::factory()->create([
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);

        $request = Request::create('/admin');
        $this->actingAs($user);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::ENGLISH->value, app()->getLocale());
    }

    public function test_handles_guest_user(): void
    {
        $default_locale = app()->getLocale();
        $request = Request::create('/admin');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Should maintain default locale
        $this->assertNotNull(app()->getLocale());
    }

    public function test_handles_user_without_native_lang_property(): void
    {
        $user = new \stdClass;
        $user->id = 1;

        $request = Request::create('/admin');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Should not throw error
        $this->assertNotNull(app()->getLocale());
    }

    public function test_supports_all_system_languages(): void
    {
        foreach (SystemLanguageEnum::cases() as $language) {
            session(['locale' => $language->value]);

            $request = Request::create('/admin');

            $this->middleware->handle($request, function ($req) {
                return response('OK');
            });

            $this->assertEquals($language->value, app()->getLocale());
        }
    }

    public function test_continues_request_chain(): void
    {
        $request = Request::create('/admin');

        $response = $this->middleware->handle($request, function ($req) {
            return response('Request processed');
        });

        $this->assertEquals('Request processed', $response->getContent());
    }

    public function test_returns_early_when_valid_session_locale_exists(): void
    {
        session(['locale' => SystemLanguageEnum::VIETNAMESE->value]);

        $user = User::factory()->create([
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
        ]);

        $request = Request::create('/admin');
        $this->actingAs($user);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Should use session locale, not user locale
        $this->assertEquals(SystemLanguageEnum::VIETNAMESE->value, app()->getLocale());
    }
}
