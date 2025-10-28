<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\ChatLesson;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\Topic\UserTopicVisibility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatLessonVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function createSystemTopic(string $label): Topic
    {
        $topic = Topic::create([
            'user_id' => null,
            'slug' => str()->slug($label).'-'.uniqid(),
            'is_system' => true,
            'is_approved' => true,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => app()->getLocale(),
            'value' => $label,
        ]);

        return $topic;
    }

    public function test_hidden_topics_are_not_listed_in_chat_component(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $visibleTopic = $this->createSystemTopic('Business');
        $hiddenTopic = $this->createSystemTopic('Travel');

        // Hide one topic for this user
        UserTopicVisibility::create([
            'user_id' => $user->id,
            'topic_id' => $hiddenTopic->id,
            'is_hidden' => true,
        ]);

        $component = Livewire::test(ChatLesson::class)
            ->assertStatus(200);

        $topics = $component->get('topics');

        $topicIds = array_map(fn (array $t): int => (int) $t['id'], $topics);

        $this->assertContains($visibleTopic->id, $topicIds, 'Visible topic should be listed.');
        $this->assertNotContains($hiddenTopic->id, $topicIds, 'Hidden topic should not be listed.');
    }
}
