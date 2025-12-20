<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Topic\TopicIndex;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TopicIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.topic.topic-index');
    }

    public function test_mount_loads_visibility_state(): void
    {
        Topic::factory()->create(['is_system' => true]);

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->assertSet('visible_topic_ids', function ($ids) {
                return is_array($ids);
            });
    }

    public function test_mount_restores_filters_from_session(): void
    {
        session()->put('topic_filters.active_tab', 'system');
        session()->put('topic_filters.search', 'test search');
        session()->put('topic_filters.sort', 'oldest');

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->assertSet('active_tab', 'system')
            ->assertSet('search', 'test search')
            ->assertSet('sort', 'oldest');
    }

    public function test_delete_topic_removes_user_owned_topic(): void
    {
        $topic = Topic::factory()->create([
            'user_id' => $this->user->id,
            'is_system' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('deleteTopic', $topic->id);

        $this->assertDatabaseMissing('topics', [
            'id' => $topic->id,
        ]);
    }

    public function test_delete_topic_does_not_delete_other_user_topic(): void
    {
        $other_user = User::factory()->create();
        $topic = Topic::factory()->create([
            'user_id' => $other_user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('deleteTopic', $topic->id);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
        ]);
    }

    public function test_delete_keyword_removes_user_owned_keyword(): void
    {
        $topic = Topic::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $keyword = Keyword::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('deleteKeyword', $keyword->id);

        $this->assertDatabaseMissing('keywords', [
            'id' => $keyword->id,
        ]);
    }

    public function test_request_delete_topic_shows_confirmation_modal(): void
    {
        $topic = Topic::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('requestDeleteTopic', $topic->id)
            ->assertSet('show_delete_topic_confirm', true)
            ->assertSet('topic_to_delete', $topic->id);
    }

    public function test_cancel_delete_topic_hides_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->set('show_delete_topic_confirm', true)
            ->set('topic_to_delete', 123)
            ->call('cancelDeleteTopic')
            ->assertSet('show_delete_topic_confirm', false)
            ->assertSet('topic_to_delete', null);
    }

    public function test_confirm_delete_topic_deletes_and_closes_modal(): void
    {
        $topic = Topic::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->set('topic_to_delete', $topic->id)
            ->call('confirmDeleteTopic')
            ->assertSet('show_delete_topic_confirm', false);

        $this->assertDatabaseMissing('topics', [
            'id' => $topic->id,
        ]);
    }

    public function test_switch_tab_changes_active_tab(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('switchTab', 'system')
            ->assertSet('active_tab', 'system');
    }

    public function test_switch_tab_saves_to_session(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('switchTab', 'system');

        $this->assertEquals('system', session('topic_filters.active_tab'));
    }

    public function test_toggle_accordion_expands_topic(): void
    {
        $topic = Topic::factory()->create();

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('toggleAccordion', $topic->id)
            ->assertSet('expanded_topics', [$topic->id]);
    }

    public function test_toggle_accordion_collapses_expanded_topic(): void
    {
        $topic = Topic::factory()->create();

        $component = Livewire::actingAs($this->user)
            ->test(TopicIndex::class);

        $component->call('toggleAccordion', $topic->id);
        $component->call('toggleAccordion', $topic->id);

        $component->assertSet('expanded_topics', []);
    }

    public function test_toggle_topic_visibility_hides_visible_topic(): void
    {
        $topic = Topic::factory()->create();

        $component = Livewire::actingAs($this->user)
            ->test(TopicIndex::class);

        $component->set('visible_topic_ids', [$topic->id]);
        $component->call('toggleTopicVisibility', $topic->id);

        $component->assertSet('visible_topic_ids', function ($ids) use ($topic) {
            return ! in_array($topic->id, $ids);
        });
    }

    public function test_toggle_topic_visibility_shows_hidden_topic(): void
    {
        $topic = Topic::factory()->create();

        $component = Livewire::actingAs($this->user)
            ->test(TopicIndex::class);

        $component->set('visible_topic_ids', []);
        $component->call('toggleTopicVisibility', $topic->id);

        $component->assertSet('visible_topic_ids', function ($ids) use ($topic) {
            return in_array($topic->id, $ids);
        });
    }

    public function test_add_topic_placeholder_adds_to_array(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->dispatch('topic-creating')
            ->assertSet('creating_topics_placeholders', function ($placeholders) {
                return count($placeholders) === 1;
            });
    }

    public function test_add_keyword_placeholder_adds_to_topic_array(): void
    {
        $topic = Topic::factory()->create();

        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->dispatch('keyword-creating', topicId: $topic->id)
            ->assertSet("creating_keywords_placeholders.{$topic->id}", function ($placeholders) {
                return count($placeholders) === 1;
            });
    }

    public function test_set_sort_updates_sort_value(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('setSort', 'oldest')
            ->assertSet('sort', 'oldest');
    }

    public function test_set_sort_saves_to_session(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->call('setSort', 'oldest');

        $this->assertEquals('oldest', session('topic_filters.sort'));
    }

    public function test_component_initializes_with_custom_tab_by_default(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->assertSet('active_tab', 'custom');
    }

    public function test_component_initializes_with_closed_modals(): void
    {
        Livewire::actingAs($this->user)
            ->test(TopicIndex::class)
            ->assertSet('show_delete_topic_confirm', false)
            ->assertSet('show_delete_keyword_confirm', false);
    }

    public function test_load_visibility_state_clears_placeholders(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(TopicIndex::class);

        $component->set('creating_topics_placeholders', ['placeholder1']);
        $component->set('creating_keywords_placeholders', [1 => ['placeholder1']]);

        $component->dispatch('topic-created');

        $component->assertSet('creating_topics_placeholders', [])
            ->assertSet('creating_keywords_placeholders', []);
    }
}
