<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_belongs_to_topic_relation(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);

        $topic = Topic::create([
            'slug' => 'general-'.uniqid(),
            'is_system' => true,
            'is_approved' => true,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => 'General',
        ]);

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'lesson_name' => 'Test Lesson',
            'lesson_text' => 'Sample',
            'grammar_data' => [],
            'vocabulary' => [],
            'dialogue' => [],
            'exercises' => [],
            'homework' => null,
            'common_mistakes' => [],
            'culture_notes' => null,
            'quiz' => [],
        ]);

        $this->assertNotNull($lesson->topic);
        $this->assertTrue($lesson->topic->is($topic));
    }
}
