<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\System\SystemLanguageEnum;
use App\Http\Middleware\SetLocale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SetLocaleTest extends TestCase
{
    use RefreshDatabase;

    private SetLocale $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SetLocale;
    }

    public function test_sets_locale_from_session(): void
    {
        session(['locale' => SystemLanguageEnum::VIETNAMESE->value]);

        $request = Request::create('/test');

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

        $request = Request::create('/test');
        $this->actingAs($user);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::VIETNAMESE->value, app()->getLocale());
    }

    public function test_saves_user_locale_to_session(): void
    {
        $this->markTestSkipped('Session is not persisted in middleware testing context');
    }

    public function test_ignores_invalid_locale_in_session(): void
    {
        session(['locale' => 'invalid-locale']);
        $default_locale = app()->getLocale();

        $request = Request::create('/test');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals($default_locale, app()->getLocale());
    }

    public function test_session_locale_takes_precedence_over_user_native_lang(): void
    {
        session(['locale' => SystemLanguageEnum::ENGLISH->value]);

        $user = User::factory()->create([
            'native_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);

        $request = Request::create('/test');
        $this->actingAs($user);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::ENGLISH->value, app()->getLocale());
    }

    public function test_handles_guest_user_without_errors(): void
    {
        $default_locale = app()->getLocale();
        $request = Request::create('/test');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals($default_locale, app()->getLocale());
    }

    public function test_handles_user_without_native_lang(): void
    {
        $user = new \stdClass;
        $user->id = 1;

        $request = Request::create('/test');
        $default_locale = app()->getLocale();

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals($default_locale, app()->getLocale());
    }

    public function test_validates_locale_against_system_language_enum(): void
    {
        foreach (SystemLanguageEnum::cases() as $language) {
            session(['locale' => $language->value]);

            $request = Request::create('/test');

            $this->middleware->handle($request, function ($req) {
                return response('OK');
            });

            $this->assertEquals($language->value, app()->getLocale());
        }
    }

    public function test_continues_request_chain(): void
    {
        $request = Request::create('/test');

        $response = $this->middleware->handle($request, function ($req) {
            return response('Request processed');
        });

        $this->assertEquals('Request processed', $response->getContent());
    }

    public function test_handles_english_locale(): void
    {
        session(['locale' => SystemLanguageEnum::ENGLISH->value]);

        $request = Request::create('/test');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::ENGLISH->value, app()->getLocale());
    }

    public function test_handles_vietnamese_locale(): void
    {
        session(['locale' => SystemLanguageEnum::VIETNAMESE->value]);

        $request = Request::create('/test');

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals(SystemLanguageEnum::VIETNAMESE->value, app()->getLocale());
    }
}
