<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\ChatLesson;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KeywordMultiSelectTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_select_multiple_keywords_and_persist_in_session_and_prompts(): void
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

        // Create two keywords under that topic with translations
        $k1 = Keyword::create([
            'topic_id' => $topic->id,
            'slug' => 'hotels',
            'is_system' => true,
            'is_hidden' => false,
        ]);
        KeywordTranslation::create([
            'keyword_id' => $k1->id,
            'language_code' => 'en',
            'value' => 'Hotels',
        ]);
        KeywordTranslation::create([
            'keyword_id' => $k1->id,
            'language_code' => 'vi',
            'value' => 'Khách sạn',
        ]);

        $k2 = Keyword::create([
            'topic_id' => $topic->id,
            'slug' => 'flights',
            'is_system' => true,
            'is_hidden' => false,
        ]);
        KeywordTranslation::create([
            'keyword_id' => $k2->id,
            'language_code' => 'en',
            'value' => 'Flights',
        ]);
        KeywordTranslation::create([
            'keyword_id' => $k2->id,
            'language_code' => 'vi',
            'value' => 'Chuyến bay',
        ]);

        // Prime session with selected topic
        session(['selected_topic_id' => $topic->id]);

        // Bind a mock for OpenAIService and assert both keyword labels are passed
        $this->mock(OpenAIService::class)
            ->shouldReceive('generateLesson')
            ->once()
            ->withArgs(function ($dto) {
                // LessonDto with topics in English locale by default
                $this->assertIsArray($dto->themes);
                $this->assertContains('Hotels', $dto->themes);
                $this->assertContains('Flights', $dto->themes);

                return true;
            })
            ->andReturn([
                'lesson_name' => 'Test Lesson',
                'lesson_text' => 'Content',
                'grammar_highlights' => [],
                'vocabulary' => [],
                'quiz' => [],
            ]);

        // Mount, select two keywords and ensure state and session contain both
        Livewire::test(ChatLesson::class)
            ->assertStatus(200)
            ->assertSet('topic_id', $topic->id)
            ->call('setKeywordIds', [$k1->id, $k2->id])
            ->assertSet('keyword_ids', [$k1->id, $k2->id])
            ->call('startLesson')
            ->assertSet('lesson_id', fn ($value) => $value === null || is_int($value));

        // Ensure session saved both ids
        $this->assertSame([$k1->id, $k2->id], array_values((array) session('selected_keyword_ids')));
    }
}
