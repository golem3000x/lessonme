<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_belongs_to_topic_relation(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
        ]);
        $user->syncLearningLanguages(['vi']);

        $topic = Topic::factory()->create();

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => 'General',
        ]);

        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
        ]);

        $this->assertNotNull($lesson->topic);
        $this->assertTrue($lesson->topic->is($topic));
    }
}
