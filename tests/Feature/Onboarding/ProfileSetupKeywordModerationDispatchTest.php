<?php

declare(strict_types=1);

namespace Tests\Feature\Onboarding;

use App\Jobs\Moderate\ModerateKeywordJob;
use App\Livewire\Onboarding\ProfileSetup;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileSetupKeywordModerationDispatchTest extends TestCase
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

    public function test_add_keyword_dispatches_moderation_job(): void
    {
        Bus::fake();

        $user = User::factory()->create([
            'native_lang' => 'en',
            'learning_lang' => 'vi',
        ]);
        $this->actingAs($user);

        $topic = $this->create_system_topic('Hobbies');

        Livewire::test(ProfileSetup::class)
            ->set('new_keyword_by_topic.'.$topic->id, 'Photography')
            ->call('addKeyword', $topic->id)
            ->assertSet('new_keyword_by_topic.'.$topic->id, '');

        Bus::assertDispatched(ModerateKeywordJob::class, function (ModerateKeywordJob $job) use ($user, $topic): bool {
            return $job->keyword->topic_id === $topic->id && $job->keyword->user_id === $user->id;
        });
    }
}
