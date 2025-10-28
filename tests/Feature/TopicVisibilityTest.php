<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\Topic\UserTopicVisibility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function createSystemTopic(string $label = 'System Topic'): Topic
    {
        $topic = Topic::create([
            'user_id' => null,
            'slug' => 'system-topic-'.uniqid(),
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

    public function test_system_topic_is_visible_by_default_and_can_be_hidden_and_shown_back(): void
    {
        $user = User::factory()->create();
        $topic = $this->createSystemTopic();

        $visible = Topic::query()->visibleForUser($user->id)->pluck('id')->all();
        $this->assertContains($topic->id, $visible, 'System topic should be visible when no visibility record exists.');

        UserTopicVisibility::create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'is_hidden' => true,
        ]);

        $visible = Topic::query()->visibleForUser($user->id)->pluck('id')->all();
        $this->assertNotContains($topic->id, $visible, 'System topic should be hidden when is_hidden=true.');

        // Show back
        UserTopicVisibility::where('user_id', $user->id)->where('topic_id', $topic->id)->update(['is_hidden' => false]);

        $visible = Topic::query()->visibleForUser($user->id)->pluck('id')->all();
        $this->assertContains($topic->id, $visible, 'System topic should be visible again when is_hidden=false.');
    }
}
