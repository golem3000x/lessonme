<?php

declare(strict_types=1);

namespace Tests\Feature\Onboarding;

use App\Livewire\Onboarding\TopicSelector;
use App\Models\Topic\Topic;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class TopicSelectorTest extends TestCase
{
    public function test_component_renders_topics_and_saves_selection(): void
    {
        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $t1 = Topic::create(['name' => 'Business']);
        $t2 = Topic::create(['name' => 'Travel']);

        $this->get(route('onboarding.topics'))
            ->assertOk()
            ->assertSee('Business')
            ->assertSee('Travel');

        Livewire::test(TopicSelector::class)
            ->set('selected', [$t1->id, $t2->id])
            ->call('save')
            ->assertRedirect(route('dashboard'));

        $this->followRedirects(
            $this->get(route('onboarding.topics'))
        )->assertSee('Topics saved: Business, Travel');
    }
}
