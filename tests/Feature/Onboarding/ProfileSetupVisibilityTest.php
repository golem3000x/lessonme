<?php

declare(strict_types=1);

namespace Tests\Feature\Onboarding;

use App\Livewire\Onboarding\ProfileSetup;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\Topic\UserTopicVisibility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileSetupVisibilityTest extends TestCase
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

    public function test_hidden_topic_checkbox_is_unchecked_after_reload(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $visible_topic = $this->createSystemTopic('Business');
        $hidden_topic = $this->createSystemTopic('Travel');

        // Mark one topic as hidden for this user
        UserTopicVisibility::create([
            'user_id' => $user->id,
            'topic_id' => $hidden_topic->id,
            'is_hidden' => true,
        ]);

        $component = Livewire::test(ProfileSetup::class)
            ->assertStatus(200);

        // available_topics should list all topics
        $available = $component->get('available_topics');
        $available_ids = array_map(fn (array $t): int => (int) $t['id'], $available);
        $this->assertContains($visible_topic->id, $available_ids);
        $this->assertContains($hidden_topic->id, $available_ids);

        // visible_topic_ids should exclude hidden_topic and include visible_topic
        $visible_ids = $component->get('visible_topic_ids');
        $this->assertContains($visible_topic->id, $visible_ids);
        $this->assertNotContains($hidden_topic->id, $visible_ids);
    }
}
