<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Enums\Lesson\LessonAudioStatusEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAudioFile;
use App\Services\FptTtsService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LessonAudioService
{
    public function __construct(
        private FptTtsService $tts_service
    ) {}

    public function generateAudioForLesson(Lesson $lesson): void
    {
        try {
            $this->generateVocabularyAudio($lesson);
            $this->generateGapFillAudio($lesson);
        } catch (Exception $e) {
            Log::error('Failed to generate audio for lesson', [
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function generateVocabularyAudio(Lesson $lesson): void
    {
        if (empty($lesson->vocabulary)) {
            return;
        }

        if ($lesson->lesson_lang === null) {
            Log::warning('Lesson has no language set', ['lesson_id' => $lesson->id]);

            return;
        }

        $voice = $this->getVoiceForLanguage($lesson->lesson_lang);
        $base_path = "lessons/$lesson->uuid/audio/vocabulary";

        foreach ($lesson->vocabulary as $vocab) {
            $word = $vocab['word'] ?? null;
            if (empty($word)) {
                continue;
            }

            try {
                $file_name = md5($word).'.mp3';
                $file_path = "$base_path/$file_name";

                $this->generateAudioFile(
                    lesson: $lesson,
                    text: $word,
                    voice: $voice,
                    audio_type: 'vocabulary',
                    audio_key: $word,
                    file_path: $file_path,
                    log_context: ['lesson_id' => $lesson->id, 'word' => $word]
                );
            } catch (Exception $e) {
                Log::error('Failed to generate vocabulary audio', [
                    'lesson_id' => $lesson->id,
                    'word' => $word,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function generateGapFillAudio(Lesson $lesson): void
    {
        if (empty($lesson->exercises['gap_fill'])) {
            return;
        }

        if ($lesson->lesson_lang === null) {
            Log::warning('Lesson has no language set', ['lesson_id' => $lesson->id]);

            return;
        }

        $voice = $this->getVoiceForLanguage($lesson->lesson_lang);
        $base_path = "lessons/$lesson->uuid/audio/exercises";

        foreach ($lesson->exercises['gap_fill'] as $index => $exercise) {
            $sentence = $exercise['sentence'] ?? null;
            $answer = $exercise['answer'] ?? null;

            if (empty($sentence) || empty($answer)) {
                continue;
            }

            try {
                // Build complete sentence with answer
                $complete_sentence = str_replace('___', $answer, $sentence);
                // Remove hint in parentheses
                $clean_sentence = preg_replace('/\s*\([^)]+\)/', '', $complete_sentence);
                $text = is_string($clean_sentence) ? $clean_sentence : $complete_sentence;

                $file_name = "gap_fill_$index.mp3";
                $file_path = "$base_path/$file_name";

                $this->generateAudioFile(
                    lesson: $lesson,
                    text: $text,
                    voice: $voice,
                    audio_type: 'gap_fill',
                    audio_key: (string) $index,
                    file_path: $file_path,
                    log_context: ['lesson_id' => $lesson->id, 'index' => $index]
                );
            } catch (Exception $e) {
                Log::error('Failed to generate gap_fill audio', [
                    'lesson_id' => $lesson->id,
                    'index' => $index,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function deleteAudioFiles(Lesson $lesson): void
    {
        try {
            // Delete physical files
            $directory = "lessons/$lesson->uuid/audio";
            if (Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->deleteDirectory($directory);
            }

            // Delete database records
            $lesson->audioFiles()->delete();

            Log::info('Deleted audio files for lesson', ['lesson_id' => $lesson->id]);
        } catch (Exception $e) {
            Log::error('Failed to delete audio files', [
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getVoiceForLanguage(SystemLanguageEnum $lang): string
    {
        return match ($lang) {
            SystemLanguageEnum::VIETNAMESE => 'banmai',
            SystemLanguageEnum::ENGLISH => 'joanna',
        };
    }

    private function downloadAudio(string $url): ?string
    {
        try {
            // Wait a bit for file to be ready on FPT servers
            sleep(2);

            $max_retries = 3;
            for ($i = 0; $i < $max_retries; $i++) {
                $content = @file_get_contents($url);
                if ($content !== false && strlen($content) > 0) {
                    return $content;
                }
                sleep(1);
            }

            return null;
        } catch (Exception $e) {
            Log::error('Failed to download audio', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $log_context
     *
     * @throws Exception
     */
    private function generateAudioFile(
        Lesson $lesson,
        string $text,
        string $voice,
        string $audio_type,
        string $audio_key,
        string $file_path,
        array $log_context
    ): void {
        // Create pending record
        $audio_file = LessonAudioFile::create([
            'lesson_id' => $lesson->id,
            'audio_type' => $audio_type,
            'audio_key' => $audio_key,
            'file_path' => '',
            'status' => LessonAudioStatusEnum::PENDING,
        ]);

        try {
            // Generate audio
            $result = $this->tts_service->synthesize($text, $voice);

            if (! $result || ! isset($result['async'])) {
                $this->markAsFailed($audio_file, 'No audio URL returned from API');

                return;
            }

            // Download audio file
            $audio_url = $result['async'];
            $audio_content = $this->downloadAudio($audio_url);

            if (! $audio_content) {
                $this->markAsFailed($audio_file, 'Failed to download audio file');

                return;
            }

            // Save to storage
            Storage::disk('public')->put($file_path, $audio_content);

            // Update record
            $audio_file->update([
                'file_path' => $file_path,
                'file_size' => strlen($audio_content),
                'status' => LessonAudioStatusEnum::READY,
            ]);

            Log::info("Generated $audio_type audio", array_merge($log_context, [
                'file_path' => $file_path,
            ]));
        } catch (Exception $e) {
            $this->markAsFailed($audio_file, $e->getMessage());
            throw $e;
        }
    }

    private function markAsFailed(LessonAudioFile $audio_file, string $error_message): void
    {
        $audio_file->update([
            'status' => LessonAudioStatusEnum::FAILED,
            'error_message' => $error_message,
        ]);
    }
}
