<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Chat\ChatLesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_selection_resets_when_topic_changes(): void
    {
        // Mount component with two topics and a pre-selected keyword id
        $component = Livewire::test(ChatLesson::class)
            ->set('topics', [
                ['id' => 1, 'label' => 'Topic A'],
                ['id' => 2, 'label' => 'Topic B'],
            ])
            ->set('topic_id', 1)
            ->set('keyword_ids', [999]);

        // Changing the topic via property update triggers updatedTopicId()
        $component->set('topic_id', 2)
            ->assertSet('keyword_ids', []);

        // Ensure the rendered heading reflects the selected topic label
        $component->assertSee('Topic B');
    }
}
