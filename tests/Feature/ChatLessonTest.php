<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Chat\ChatLesson;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_selection_resets_when_topic_changes(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $t1 = Topic::create([
            'slug' => 'topic-a',
            'is_system' => true,
            'is_approved' => true,
        ]);
        TopicTranslation::create([
            'topic_id' => $t1->id,
            'language_code' => 'en',
            'value' => 'Topic A',
        ]);

        $t2 = Topic::create([
            'slug' => 'topic-b',
            'is_system' => true,
            'is_approved' => true,
        ]);
        TopicTranslation::create([
            'topic_id' => $t2->id,
            'language_code' => 'en',
            'value' => 'Topic B',
        ]);

        // Session prime with t1
        session(['selected_topic_id' => $t1->id]);

        // Mount component - it should load t1 from session
        $component = Livewire::test(ChatLesson::class)
            ->assertSet('topic_id', $t1->id)
            ->call('setKeywordIds', [999])
            ->assertSet('keyword_ids', [999]);

        // Changing the topic via property update triggers updatedTopicId()
        $component->set('topic_id', $t2->id)
            ->assertSet('topic_id', $t2->id)
            ->assertSet('keyword_ids', []);
    }
}
