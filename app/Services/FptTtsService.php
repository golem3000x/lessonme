<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FptTtsService
{
    private const string FPT_TTS_API_URL = 'https://api.fpt.ai/hmi/tts/v5';
    private string $fpt_tts_api_key;

    public function __construct()
    {
        $this->fpt_tts_api_key = config('services.fpt_tts.api_key');
    }

    public function synthesize(string $text, string $voice = 'banmai'): ?array
    {
        if (empty($this->fpt_tts_api_key)) {
            Log::error('FPT TTS API key is not configured');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $this->fpt_tts_api_key,
                'speed' => '-1',
            ])->post(self::FPT_TTS_API_URL, [
                'text' => $text,
                'voice' => $voice
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('FPT TTS API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('FPT TTS API exception', [
                'message' => $e->getMessage(),
                'text' => $text,
                'voice' => $voice
            ]);
            return null;
        }
    }
}