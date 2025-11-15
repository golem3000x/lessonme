<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use App\Services\Slug\SlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createTopic(string $label): Topic
    {
        $topic = Topic::create([
            'user_id' => null,
            'slug' => str()->slug($label).'-'.uniqid(),
            'is_system' => true,
            'is_approved' => true,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => $label,
        ]);

        return $topic;
    }

    private function fakeOpenAI(): void
    {
        $this->mock(SlugService::class, function ($mock): void {
            $mock->shouldReceive('generateLesson')
                ->andReturn([
                    'lesson_name' => 'Test Lesson',
                    'lesson_text' => 'Generated lesson',
                    'grammar_highlights' => [],
                    'vocabulary' => [],
                    'quiz' => [],
                ]);
        });
    }

    public function test_store_with_topic_label_only_resolves_topic_id(): void
    {
        $this->withoutMiddleware();
        $this->fakeOpenAI();

        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = $this->createTopic('Business');

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
        $this->withoutMiddleware();
        $this->fakeOpenAI();

        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = $this->createTopic('Travel');

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
        $this->withoutMiddleware();
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
