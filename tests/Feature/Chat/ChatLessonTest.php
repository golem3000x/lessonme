<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

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

    public function test_updated_topic_id_updates_session_and_label(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        // Create two visible topics with translations
        $t1 = Topic::create([
            'slug' => 'business',
            'is_system' => true,
        ]);
        TopicTranslation::create([
            'topic_id' => $t1->id,
            'language_code' => app()->getLocale(),
            'value' => 'Business',
        ]);

        $t2 = Topic::create([
            'slug' => 'travel',
            'is_system' => true,
            'is_hidden' => false,
        ]);
        TopicTranslation::create([
            'topic_id' => $t2->id,
            'language_code' => app()->getLocale(),
            'value' => 'Travel',
        ]);

        // Mount component and change the topic_id
        Livewire::test(ChatLesson::class)
            ->assertStatus(200)
            ->set('topic_id', $t2->id) // triggers updatedTopicId
            ->assertSet('topic_id', $t2->id)
            ->assertSet('topic', 'Travel');

        $this->assertSame($t2->id, (int) session('selected_topic_id'));
    }
}
