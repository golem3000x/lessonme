<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\ChatLesson;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KeywordSelectionPersistsAcrossLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_keyword_persists_after_locale_change(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        // Create a visible topic with translations in EN and VI
        $topic = Topic::create([
            'slug' => 'travel',
            'is_system' => true,
            'is_hidden' => false,
        ]);
        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => 'Travel',
        ]);
        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'vi',
            'value' => 'Du lịch',
        ]);

        // Create a keyword under that topic with translations
        $keyword = Keyword::create([
            'topic_id' => $topic->id,
            'slug' => 'hotels',
            'is_system' => true,
            'is_hidden' => false,
        ]);
        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => 'en',
            'value' => 'Hotels',
        ]);
        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => 'vi',
            'value' => 'Khách sạn',
        ]);

        // Prime session with selected topic
        session(['selected_topic_id' => $topic->id]);

        // Mount and select keyword
        Livewire::test(ChatLesson::class)
            ->assertStatus(200)
            ->assertSet('topic_id', $topic->id)
            ->call('setKeywordIds', [$keyword->id])
            ->assertSet('keyword_ids', [$keyword->id]);

        // Ensure session saved
        $this->assertSame([$keyword->id], array_values((array) session('selected_keyword_ids')));

        // Simulate locale change (like middleware would)
        session(['locale' => 'vi']);
        app()->setLocale('vi');

        // Remount component (new request) and ensure keyword persists
        Livewire::test(ChatLesson::class)
            ->assertStatus(200)
            ->assertSet('keyword_ids', [$keyword->id])
            ->assertSet('topic_id', $topic->id);

        // Also ensure topic label is set (in either lang); not asserting exact string to avoid coupling
        $this->assertNotSame('', (string) Livewire::test(ChatLesson::class)->get('topic'));
    }
}
