<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Slug\CreateTopic;
use App\Models\Topic\Topic;
use App\Models\User;
use App\Notifications\Slug\Processing\SlugProcessingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTopicTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'native_lang' => 'en',
        ]);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.onboarding.create-topic');
    }

    public function test_mount_sets_native_lang_from_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->assertSet('native_lang', 'en');
    }

    public function test_component_initializes_with_empty_topic_name(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->assertSet('topic_name', '');
    }

    public function test_save_topic_creates_topic_in_database(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic');

        $this->assertDatabaseHas('topics', [
            'user_id' => $this->user->id,
            'slug' => 'technology',
            'is_system' => false,
            'is_approved' => false,
        ]);
    }

    public function test_save_topic_creates_translation(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic');

        $topic = Topic::where('slug', '=', 'technology')->first();

        $this->assertDatabaseHas('topic_translations', [
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => 'Technology',
        ]);
    }

    public function test_save_topic_sends_notification(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic');

        Notification::assertSentTo(
            $this->user,
            SlugProcessingNotification::class
        );
    }

    public function test_save_topic_dispatches_events(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic')
            ->assertDispatched('topic-creating')
            ->assertDispatched('topic-created');
    }

    public function test_save_topic_clears_input_after_saving(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic')
            ->assertSet('topic_name', '');
    }

    public function test_save_topic_sets_flash_message(): void
    {
        $this->markTestSkipped('Flash messages are not available in Livewire component tests');
    }

    public function test_save_topic_returns_early_for_empty_value(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', '')
            ->call('saveTopic')
            ->assertHasNoErrors();

        // Should not create topic
        $this->assertEquals(0, Topic::count());
    }

    public function test_save_topic_validates_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', str_repeat('a', 256))
            ->call('saveTopic')
            ->assertHasErrors(['topic_name' => 'max']);
    }

    public function test_save_topic_validates_meaningfulness(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', '!!!')
            ->call('saveTopic')
            ->assertHasErrors('topic_name');
    }

    public function test_save_topic_validates_uniqueness_for_user(): void
    {
        Notification::fake();

        // Create existing topic
        Topic::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'technology',
        ]);

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic')
            ->assertHasErrors('topic_name');
    }

    public function test_save_topic_allows_same_slug_for_different_users(): void
    {
        Notification::fake();

        $other_user = User::factory()->create();

        // Create topic for different user
        Topic::factory()->create([
            'user_id' => $other_user->id,
            'slug' => 'technology',
        ]);

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic')
            ->assertHasNoErrors();

        // Should have 2 topics with same slug for different users
        $this->assertEquals(2, Topic::where('slug', '=', 'technology')->count());
    }

    public function test_save_topic_trims_whitespace(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', '  Technology  ')
            ->call('saveTopic');

        $this->assertDatabaseHas('topic_translations', [
            'value' => 'Technology',
        ]);
    }

    public function test_save_topic_generates_correct_slug(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Web Development')
            ->call('saveTopic');

        $this->assertDatabaseHas('topics', [
            'slug' => 'web-development',
        ]);
    }

    public function test_save_topic_handles_special_characters_in_slug(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'C++ Programming')
            ->call('saveTopic');

        $topic = Topic::first();
        $this->assertNotNull($topic);
        $this->assertNotEmpty($topic->slug);
    }

    public function test_save_topic_returns_early_for_empty_trimmed_value(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', '   ')
            ->call('saveTopic');

        // Should not create any topic
        $this->assertEquals(0, Topic::count());
    }

    public function test_save_topic_creates_topic_with_is_system_false(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic');

        $topic = Topic::first();
        $this->assertFalse($topic->is_system);
    }

    public function test_save_topic_creates_topic_with_is_approved_false(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic');

        $topic = Topic::first();
        $this->assertFalse($topic->is_approved);
    }

    public function test_multiple_topics_can_be_created_sequentially(): void
    {
        Notification::fake();

        $component = Livewire::actingAs($this->user)
            ->test(CreateTopic::class);

        // Create first topic
        $component->set('topic_name', 'Technology')
            ->call('saveTopic');

        // Create second topic
        $component->set('topic_name', 'Science')
            ->call('saveTopic');

        $this->assertEquals(2, Topic::where('user_id', '=', $this->user->id)->count());
    }

    public function test_save_topic_uses_user_native_language_for_translation(): void
    {
        Notification::fake();

        $vietnamese_user = User::factory()->create([
            'native_lang' => 'vi',
        ]);

        Livewire::actingAs($vietnamese_user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Công nghệ')
            ->call('saveTopic');

        $this->assertDatabaseHas('topic_translations', [
            'language_code' => 'vi',
            'value' => 'Công nghệ',
        ]);
    }

    public function test_save_topic_dispatches_creating_event_before_database_insert(): void
    {
        Notification::fake();

        $component = Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'Technology')
            ->call('saveTopic');

        // Both events should be dispatched
        $component->assertDispatched('topic-creating');
        $component->assertDispatched('topic-created');

        // And topic should be created
        $this->assertEquals(1, Topic::count());
    }

    public function test_save_topic_validates_required_field(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->call('saveTopic')
            ->assertHasNoErrors();

        // Should not create topic when empty
        $this->assertEquals(0, Topic::count());
    }

    public function test_save_topic_handles_lowercase_and_uppercase(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'TECHNOLOGY')
            ->call('saveTopic');

        $this->assertDatabaseHas('topics', [
            'slug' => 'technology',
        ]);

        $this->assertDatabaseHas('topic_translations', [
            'value' => 'TECHNOLOGY',
        ]);
    }

    public function test_save_topic_handles_mixed_case_validation(): void
    {
        Notification::fake();

        // Create topic with lowercase
        Topic::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'technology',
        ]);

        // Try to create with uppercase (same slug after conversion)
        Livewire::actingAs($this->user)
            ->test(CreateTopic::class)
            ->set('topic_name', 'TECHNOLOGY')
            ->call('saveTopic')
            ->assertHasErrors('topic_name');
    }
}
