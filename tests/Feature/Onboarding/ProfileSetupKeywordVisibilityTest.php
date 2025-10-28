<?php

declare(strict_types=1);

namespace Tests\Feature\Onboarding;

use App\Livewire\Onboarding\ProfileSetup;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Keyword\UserKeywordVisibility as UserKeywordVisibilityModel;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileSetupKeywordVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function create_system_topic(string $label): Topic
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

    protected function create_keyword(Topic $topic, string $value): Keyword
    {
        $keyword = Keyword::create([
            'topic_id' => $topic->id,
            'slug' => str()->slug($value).'-'.uniqid(),
            'is_system' => true,
        ]);

        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => app()->getLocale(),
            'value' => $value,
        ]);

        return $keyword;
    }

    public function test_hidden_keyword_checkbox_is_unchecked_after_reload(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = $this->create_system_topic('Travel');
        $visible_kw = $this->create_keyword($topic, 'Hotels');
        $hidden_kw = $this->create_keyword($topic, 'Flights');

        // Mark one keyword as hidden for this user
        UserKeywordVisibilityModel::create([
            'user_id' => $user->id,
            'keyword_id' => $hidden_kw->id,
            'is_hidden' => true,
        ]);

        $component = Livewire::test(ProfileSetup::class)
            ->assertStatus(200);

        // topic_keywords should list both keywords under the topic
        $topic_keywords = $component->get('topic_keywords');
        $this->assertArrayHasKey($topic->id, $topic_keywords);
        $ids = array_map(fn (array $k): int => (int) $k['id'], $topic_keywords[$topic->id]);
        $this->assertContains($visible_kw->id, $ids);
        $this->assertContains($hidden_kw->id, $ids);

        // visible_keyword_ids should include only visible_kw
        $visible_ids = $component->get('visible_keyword_ids');
        $this->assertContains($visible_kw->id, $visible_ids);
        $this->assertNotContains($hidden_kw->id, $visible_ids);
    }

    public function test_toggling_keyword_visibility_and_save_persists_records(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = $this->create_system_topic('Business');
        $k1 = $this->create_keyword($topic, 'Marketing');
        $k2 = $this->create_keyword($topic, 'Finance');

        // Initially all visible (no records yet)
        Livewire::test(ProfileSetup::class)
            ->set('visible_keyword_ids', [$k1->id]) // Hide k2 by omitting it
            ->call('save')
            ->assertRedirect(route('onboarding.topics'));

        $this->assertDatabaseHas('user_keyword_visibility', [
            'user_id' => $user->id,
            'keyword_id' => $k2->id,
            'is_hidden' => true,
        ]);
        $this->assertDatabaseHas('user_keyword_visibility', [
            'user_id' => $user->id,
            'keyword_id' => $k1->id,
            'is_hidden' => false,
        ]);
    }

    public function test_add_keyword_action_creates_keyword_and_updates_state(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = $this->create_system_topic('Culture');

        Livewire::test(ProfileSetup::class)
            ->set('new_keyword_by_topic.'.$topic->id, 'Museums')
            ->call('addKeyword', $topic->id)
            ->assertSet('new_keyword_by_topic.'.$topic->id, '')
            ->assertSet('topic_keywords.'.$topic->id, fn ($value) => collect($value)->pluck('label')->contains('Museums'))
            ->assertSet('visible_keyword_ids', fn ($arr) => in_array(Keyword::whereHas('translations', function ($q): void { $q->where("value", 'Museums'); })->latest('id')->first()->id, $arr, true));

        $this->assertDatabaseHas('keywords', [
            // slug will be derived from value
            'topic_id' => $topic->id,
        ]);
        $this->assertDatabaseHas('keyword_translations', [
            'language_code' => 'en',
            'value' => 'Museums',
        ]);
        $kid = Keyword::whereHas('translations', function ($q): void { $q->where('value', 'Museums'); })->first()?->id;
        $this->assertNotNull($kid);
        $this->assertDatabaseHas('user_keyword_visibility', [
            'user_id' => $user->id,
            'keyword_id' => $kid,
            'is_hidden' => false,
        ]);
    }
}
