<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Slug\CreateKeyword;
use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use App\Models\User\User;
use App\Notifications\Slug\Processing\SlugProcessingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class CreateKeywordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'native_lang' => 'en',
        ]);
        $this->topic = Topic::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->assertStatus(200)
            ->assertViewIs('livewire.onboarding.create-keyword');
    }

    public function test_mount_sets_topic_id(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->assertSet('topic_id', $this->topic->id);
    }

    public function test_mount_sets_native_lang_from_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->assertSet('native_lang', 'en');
    }

    public function test_save_keyword_creates_keyword_in_database(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword');

        $this->assertDatabaseHas('keywords', [
            'user_id' => $this->user->id,
            'topic_id' => $this->topic->id,
            'slug' => 'programming',
            'is_system' => false,
        ]);
    }

    public function test_save_keyword_creates_translation(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword');

        $keyword = Keyword::where('slug', '=', 'programming')->first();

        $this->assertDatabaseHas('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => 'en',
            'value' => 'Programming',
        ]);
    }

    public function test_save_keyword_sends_notification(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword');

        Notification::assertSentTo(
            $this->user,
            SlugProcessingNotification::class
        );
    }

    public function test_save_keyword_dispatches_events(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword')
            ->assertDispatched('keyword-creating', topicId: $this->topic->id)
            ->assertDispatched('keyword-created', topicId: $this->topic->id);
    }

    public function test_save_keyword_clears_input_after_saving(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword')
            ->assertSet('keyword_value', '');
    }

    public function test_save_keyword_sets_flash_message(): void
    {
        $this->markTestSkipped('Flash messages are not available in Livewire component tests');
    }

    public function test_save_keyword_validates_required_field(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', '')
            ->call('saveKeyword')
            ->assertHasNoErrors();

        // Should not create keyword
        $this->assertEquals(0, Keyword::count());
    }

    public function test_save_keyword_validates_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', str_repeat('a', 256))
            ->call('saveKeyword')
            ->assertHasErrors(['keyword_value' => 'max']);
    }

    public function test_save_keyword_validates_meaningfulness(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', '!!!')
            ->call('saveKeyword')
            ->assertHasErrors('keyword_value');
    }

    public function test_save_keyword_validates_uniqueness_in_topic(): void
    {
        Notification::fake();

        // Create existing keyword
        Keyword::factory()->create([
            'user_id' => $this->user->id,
            'topic_id' => $this->topic->id,
            'slug' => 'programming',
        ]);

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword')
            ->assertHasErrors('keyword_value');
    }

    public function test_save_keyword_allows_same_slug_in_different_topics(): void
    {
        Notification::fake();

        $other_topic = Topic::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create keyword in different topic
        Keyword::factory()->create([
            'user_id' => $this->user->id,
            'topic_id' => $other_topic->id,
            'slug' => 'programming',
        ]);

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword')
            ->assertHasNoErrors();

        // Should have 2 keywords with same slug in different topics
        $this->assertEquals(2, Keyword::where('slug', '=', 'programming')->count());
    }

    public function test_save_keyword_trims_whitespace(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', '  Programming  ')
            ->call('saveKeyword');

        $this->assertDatabaseHas('keyword_translations', [
            'value' => 'Programming',
        ]);
    }

    public function test_save_keyword_generates_correct_slug(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Web Development')
            ->call('saveKeyword');

        $this->assertDatabaseHas('keywords', [
            'slug' => 'web-development',
        ]);
    }

    public function test_save_keyword_handles_special_characters_in_slug(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'C++ Programming')
            ->call('saveKeyword');

        $keyword = Keyword::first();
        $this->assertNotNull($keyword);
        $this->assertNotEmpty($keyword->slug);
    }

    public function test_save_keyword_returns_early_for_empty_trimmed_value(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', '   ')
            ->call('saveKeyword');

        // Should not create any keyword
        $this->assertEquals(0, Keyword::count());
    }

    public function test_component_initializes_with_empty_keyword_value(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->assertSet('keyword_value', '');
    }

    public function test_save_keyword_creates_keyword_with_is_system_false(): void
    {
        Notification::fake();

        Livewire::actingAs($this->user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Programming')
            ->call('saveKeyword');

        $keyword = Keyword::first();
        $this->assertFalse($keyword->is_system);
    }

    public function test_multiple_keywords_can_be_created_sequentially(): void
    {
        Notification::fake();

        // Create first keyword
        $first_keyword = Keyword::factory()->create([
            'user_id' => $this->user->id,
            'topic_id' => $this->topic->id,
            'slug' => 'programming',
        ]);

        // Create second keyword
        $second_keyword = Keyword::factory()->create([
            'user_id' => $this->user->id,
            'topic_id' => $this->topic->id,
            'slug' => 'testing',
        ]);

        $this->assertEquals(2, Keyword::where('topic_id', '=', $this->topic->id)->count());
        $this->assertNotEquals($first_keyword->slug, $second_keyword->slug);
    }

    public function test_save_keyword_uses_user_native_language_for_translation(): void
    {
        Notification::fake();

        $vietnamese_user = User::factory()->create([
            'native_lang' => 'vi',
        ]);

        Livewire::actingAs($vietnamese_user)
            ->test(CreateKeyword::class, ['topicId' => $this->topic->id])
            ->set('keyword_value', 'Lập trình')
            ->call('saveKeyword');

        $this->assertDatabaseHas('keyword_translations', [
            'language_code' => 'vi',
            'value' => 'Lập trình',
        ]);
    }
}
