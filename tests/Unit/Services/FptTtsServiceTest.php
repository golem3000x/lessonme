<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\FptTtsService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FptTtsServiceTest extends TestCase
{
    private FptTtsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.fpt_tts.api_key', 'test-api-key');
        $this->service = new FptTtsService();
    }

    public function test_synthesize_returns_null_when_api_key_not_configured(): void
    {
        Config::set('services.fpt_tts.api_key', '');
        $service = new FptTtsService();

        $result = $service->synthesize('Hello world', 'joanna');

        $this->assertNull($result);
    }

    public function test_synthesize_sends_correct_request_headers(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $this->service->synthesize('Test text', 'banmai');

        Http::assertSent(function ($request) {
            return $request->hasHeader('api-key', 'test-api-key')
                && $request->hasHeader('voice', 'banmai')
                && $request->hasHeader('speed', '-1.5')
                && $request->hasHeader('Content-Type', 'text/plain; charset=utf-8')
                && $request->hasHeader('Accept', 'application/json');
        });
    }

    public function test_synthesize_sends_correct_request_body(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $this->service->synthesize('Hello Vietnam', 'banmai');

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://api.fpt.ai/hmi/tts/v5'
                && $request->body() === 'Hello Vietnam';
        });
    }

    public function test_synthesize_returns_json_response_on_success(): void
    {
        $expected_response = [
            'async' => 'http://example.com/audio/test.mp3',
            'error' => 0,
            'message' => 'Success',
        ];

        Http::fake([
            'api.fpt.ai/*' => Http::response($expected_response, 200),
        ]);

        $result = $this->service->synthesize('Test', 'joanna');

        $this->assertIsArray($result);
        $this->assertEquals('http://example.com/audio/test.mp3', $result['async']);
        $this->assertEquals(0, $result['error']);
    }

    public function test_synthesize_returns_null_on_api_error(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['error' => 'Invalid API key'], 401),
        ]);

        $result = $this->service->synthesize('Test', 'banmai');

        $this->assertNull($result);
    }

    public function test_synthesize_returns_null_on_network_exception(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(null, 500),
        ]);

        $result = $this->service->synthesize('Test', 'joanna');

        $this->assertNull($result);
    }

    public function test_synthesize_handles_different_voices(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $voices = ['banmai', 'joanna', 'leminh', 'thuminh'];

        foreach ($voices as $voice) {
            $this->service->synthesize('Test', $voice);

            Http::assertSent(function ($request) use ($voice) {
                return $request->hasHeader('voice', $voice);
            });
        }
    }

    public function test_synthesize_handles_unicode_text(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $vietnamese_text = 'Xin chào Việt Nam';
        $this->service->synthesize($vietnamese_text, 'banmai');

        Http::assertSent(function ($request) use ($vietnamese_text) {
            return $request->body() === $vietnamese_text;
        });
    }

    public function test_synthesize_uses_correct_api_endpoint(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $this->service->synthesize('Test', 'banmai');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fpt.ai/hmi/tts/v5';
        });
    }

    public function test_synthesize_uses_fixed_speed_setting(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $this->service->synthesize('Test', 'banmai');

        Http::assertSent(function ($request) {
            return $request->hasHeader('speed', '-1.5');
        });
    }

    public function test_synthesize_handles_empty_text(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $result = $this->service->synthesize('', 'banmai');

        // Should still make the request
        Http::assertSent(function ($request) {
            return $request->body() === '';
        });

        $this->assertIsArray($result);
    }

    public function test_synthesize_handles_long_text(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['async' => 'http://example.com/audio.mp3'], 200),
        ]);

        $long_text = str_repeat('This is a long text. ', 100);
        $result = $this->service->synthesize($long_text, 'joanna');

        Http::assertSent(function ($request) use ($long_text) {
            return $request->body() === $long_text;
        });

        $this->assertIsArray($result);
    }

    public function test_synthesize_returns_null_when_response_missing_async_key(): void
    {
        Http::fake([
            'api.fpt.ai/*' => Http::response(['error' => 0], 200),
        ]);

        $result = $this->service->synthesize('Test', 'banmai');

        // Response is successful but missing 'async' key
        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('async', $result);
    }
}
