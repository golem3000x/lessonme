<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Topic\Topic;
use App\Models\User;
use App\Services\OpenAi\OpenAIService;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    private function fakeOpenAI(): void
    {
        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('generateLesson')
                ->andReturn([
                    'lesson_text' => 'Generated lesson',
                    'grammar_highlights' => [],
                    'vocabulary' => [],
                    'quiz' => [],
                ]);
        });
    }

    public function test_store_with_topic_label_only_resolves_topic_id(): void
    {
        $this->fakeOpenAI();

        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = Topic::create(['name' => 'Business']);

        $response = $this->post(route('lessons.store'), [
            'topic' => 'Business',
            'level' => 'beginner',
            'themes' => [],
        ]);

        $lesson = Lesson::first();
        $this->assertNotNull($lesson);
        $this->assertSame($topic->id, $lesson->topic_id);
        $response->assertRedirect(route('lessons.show', $lesson->id));
    }

    public function test_store_with_topic_id_only_persists_topic_id(): void
    {
        $this->fakeOpenAI();

        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = Topic::create(['name' => 'Travel']);

        $response = $this->post(route('lessons.store'), [
            'topic_id' => $topic->id,
            'level' => 'intermediate',
        ]);

        $lesson = Lesson::first();
        $this->assertNotNull($lesson);
        $this->assertSame($topic->id, $lesson->topic_id);
        $response->assertRedirect(route('lessons.show', $lesson->id));
    }

    public function test_store_without_topic_defaults_and_allows_null_topic_id(): void
    {
        $this->fakeOpenAI();

        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $response = $this->post(route('lessons.store'), [
            'level' => 'advanced',
        ]);

        $lesson = Lesson::first();
        $this->assertNotNull($lesson);
        $this->assertNull($lesson->topic_id);
        $response->assertRedirect(route('lessons.show', $lesson->id));
    }
}
