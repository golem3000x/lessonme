<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Lesson\LessonSpeaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonSpeakerTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully(): void
    {
        Livewire::test(LessonSpeaker::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.lesson.lesson-speaker');
    }

    public function test_mount_sets_audio_url(): void
    {
        $audio_url = 'https://example.com/audio.mp3';

        Livewire::test(LessonSpeaker::class, ['audio_url' => $audio_url])
            ->assertSet('audio_url', $audio_url);
    }

    public function test_mount_handles_null_audio_url(): void
    {
        Livewire::test(LessonSpeaker::class, ['audio_url' => null])
            ->assertSet('audio_url', null);
    }

    public function test_mount_defaults_to_null_when_no_audio_url_provided(): void
    {
        Livewire::test(LessonSpeaker::class)
            ->assertSet('audio_url', null);
    }

    public function test_component_accepts_different_audio_urls(): void
    {
        $url1 = 'https://example.com/audio1.mp3';
        $url2 = 'https://example.com/audio2.mp3';

        Livewire::test(LessonSpeaker::class, ['audio_url' => $url1])
            ->assertSet('audio_url', $url1);

        Livewire::test(LessonSpeaker::class, ['audio_url' => $url2])
            ->assertSet('audio_url', $url2);
    }

    public function test_component_handles_storage_url_format(): void
    {
        $storage_url = 'storage/audio/lesson-123.mp3';

        Livewire::test(LessonSpeaker::class, ['audio_url' => $storage_url])
            ->assertSet('audio_url', $storage_url);
    }
}
