<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Moderate\ModerateKeywordJob;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Topic\Topic;
use App\Models\Topic\TopicTranslation;
use App\Models\User;
use App\Services\Slug\SlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ModerateKeywordJobTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(): User
    {
        return User::factory()->create([
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
            'learning_lang' => SystemLanguageEnum::VIETNAMESE->value,
        ]);
    }

    protected function createTopic(?int $user_id = null): Topic
    {
        $topic = Topic::create([
            'user_id' => $user_id,
            'slug' => 'travel-'.uniqid(),
            'is_system' => $user_id === null,
            'is_approved' => true,
        ]);

        TopicTranslation::create([
            'topic_id' => $topic->id,
            'language_code' => SystemLanguageEnum::ENGLISH->value,
            'value' => 'Travel',
        ]);

        return $topic;
    }

    protected function createKeyword(User $user, Topic $topic, string $value = 'Cafe'): Keyword
    {
        $keyword = Keyword::create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'slug' => str()->slug($value).'-'.uniqid(),
            'is_system' => false,
        ]);

        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => $user->native_lang,
            'value' => $value,
        ]);

        return $keyword;
    }

    public function test_approved_keyword_gets_translated_into_learning_language(): void
    {
        $user = $this->createUser();
        $topic = $this->createTopic($user->id);
        $keyword = $this->createKeyword($user, $topic, 'Cafe');

        $openAI = Mockery::mock(SlugService::class);
        $openAI->shouldReceive('moderateSlug')->once()->with('Cafe')->andReturn(true);
        $openAI->shouldReceive('translateSlugAllLanguages')->once()->with('Cafe')->andReturn([
            SystemLanguageEnum::VIETNAMESE->value => 'Quán cà phê',
        ]);

        $job = new ModerateKeywordJob($keyword);
        $job->handle($openAI);

        $this->assertDatabaseHas('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => SystemLanguageEnum::VIETNAMESE->value,
            'value' => 'Quán cà phê',
        ]);
    }

    public function test_rejected_keyword_does_not_get_translated(): void
    {
        $user = $this->createUser();
        $topic = $this->createTopic($user->id);
        $keyword = $this->createKeyword($user, $topic, 'Restricted');

        $openAI = Mockery::mock(SlugService::class);
        $openAI->shouldReceive('moderateSlug')->once()->with('Restricted')->andReturn(false);
        $openAI->shouldNotReceive('translateText');

        $job = new ModerateKeywordJob($keyword);
        $job->handle($openAI);

        $this->assertDatabaseMissing('keyword_translations', [
            'keyword_id' => $keyword->id,
            'language_code' => SystemLanguageEnum::VIETNAMESE->value,
        ]);
    }
}
