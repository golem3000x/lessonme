<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Enums\System\SystemLanguageEnum;
use App\Services\FptTtsService;
use Livewire\Component;

class LessonSpeaker extends Component
{
    public string $word = '';
    public string $lang = '';
    public ?string $audio_url = null;
    public bool $is_loading = false;

    public function mount(string $word, ?string $lang = null): void
    {
        $this->word = $word;
        $this->lang = $lang ?? SystemLanguageEnum::VIETNAMESE->value;
    }

    public function speak(FptTtsService $tts_service): void
    {
        // Reset audio_url to allow repeated clicks
        $this->audio_url = null;
        $this->is_loading = true;

        // Trim whitespace from word
        $word_to_speak = trim($this->word);

        // Определяем голос в зависимости от языка
        $voice = match ($this->lang) {
            SystemLanguageEnum::VIETNAMESE->value => 'banmai', // Vietnamese female voice
            SystemLanguageEnum::ENGLISH->value => 'joanna', // English female voice
            default => 'banmai',
        };

        $result = $tts_service->synthesize($word_to_speak, $voice);

        $this->is_loading = false;

        if ($result && isset($result['async'])) {
            // API возвращает ссылку на mp3
            // Alpine.js будет следить за изменением через @entangle и автоматически воспроизведет
            $this->audio_url = $result['async'];
        }
    }

    public function render()
    {
        return view('livewire.lesson.lesson-speaker');
    }
}