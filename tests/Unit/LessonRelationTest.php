<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Lesson;
use App\Models\Topic\Topic;
use App\Models\User;
use Tests\TestCase;

class LessonRelationTest extends TestCase
{
    public function test_lesson_belongs_to_topic_relation(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);

        $topic = Topic::create(['name' => 'General']);

        $lesson = Lesson::create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'lesson_text' => 'Sample',
            'grammar_data' => [],
            'vocabulary' => [],
            'quiz' => [],
        ]);

        $this->assertNotNull($lesson->topicRelation);
        $this->assertTrue($lesson->topicRelation->is($topic));
    }
}
