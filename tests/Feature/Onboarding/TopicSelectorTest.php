<?php

declare(strict_types=1);

namespace Tests\Feature\Onboarding;

use App\Livewire\Onboarding\TopicSelector;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TopicSelectorTest extends TestCase
{
    use RefreshDatabase;

    protected function createTopic(string $label): Topic
    {
        $topic = Topic::create([
            'user_id' => null,
            'slug' => str()->slug($label).'-'.uniqid(),
            'is_system' => true,
            'is_approved' => true,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'en',
            'value' => $label,
        ]);
        
        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => 'vi',
            'value' => $label,
        ]);

        return $topic;
    }

    public function test_component_renders_topics_and_saves_selection(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $t1 = $this->createTopic('Business');
        $t2 = $this->createTopic('Travel');

        $this->get(route('onboarding.topics'))
            ->assertOk()
            ->assertSee('Business')
            ->assertSee('Travel');

        Livewire::test(TopicSelector::class)
            ->set('selected', [$t1->id, $t2->id])
            ->call('save')
            ->assertRedirect(route('dashboard'));

        // Check that the session has the flash message
        $this->assertEquals('Topics saved: Business, Travel', session('message'));
    }
}
