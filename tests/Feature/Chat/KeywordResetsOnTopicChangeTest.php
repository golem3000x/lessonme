<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\ChatLesson;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KeywordResetsOnTopicChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function createTopic(string $slug, string $label): Topic
    {
        $topic = Topic::create([
            'slug' => $slug,
            'is_system' => true,
            'is_hidden' => false,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => app()->getLocale(),
            'value' => $label,
        ]);

        return $topic;
    }

    protected function createKeyword(Topic $topic, string $slug, string $label): Keyword
    {
        $keyword = Keyword::create([
            'topic_id' => $topic->id,
            'slug' => $slug,
            'is_system' => true,
            'is_hidden' => false,
        ]);

        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => app()->getLocale(),
            'value' => $label,
        ]);

        return $keyword;
    }

    public function test_keywords_reset_when_topic_changes(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $t1 = $this->createTopic('business', 'Business');
        $t2 = $this->createTopic('travel', 'Travel');

        $this->createKeyword($t1, 'negotiations', 'Negotiations');
        $this->createKeyword($t1, 'marketing', 'Marketing');

        $this->createKeyword($t2, 'beaches', 'Beaches');
        $this->createKeyword($t2, 'flights', 'Flights');

        // Mount ChatLesson and ensure keyword_ids reset upon topic change
        Livewire::test(ChatLesson::class)
            ->assertStatus(200)
            // set to topic 1 first
            ->set('topic_id', $t1->id)
            ->assertSet('topic_id', $t1->id)
            // select some keywords (simulating prior selection)
            ->call('setKeywordIds', [1, 2])
            ->assertSet('keyword_ids', [1, 2])
            // Change to topic 2, expect keyword_ids to reset to empty
            ->set('topic_id', $t2->id)
            ->assertSet('topic_id', $t2->id)
            ->assertSet('keyword_ids', []);
    }

    public function test_keyword_becomes_null_if_new_topic_has_no_keywords(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $t1 = $this->createTopic('art', 'Art');
        $t2 = $this->createTopic('empty-topic', 'Empty Topic');

        $this->createKeyword($t1, 'painting', 'Painting');

        Livewire::test(ChatLesson::class)
            ->assertStatus(200)
            ->set('topic_id', $t1->id)
            ->assertSet('topic_id', $t1->id)
            // Switch to topic with no keywords
            ->set('topic_id', $t2->id)
            ->assertSet('topic_id', $t2->id)
            ->tap(function ($component): void {
                $this->assertSame([], $component->keyword_ids);
            });
    }
}
