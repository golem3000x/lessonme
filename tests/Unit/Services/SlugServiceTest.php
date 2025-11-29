<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Slug\SlugService;
use Mockery;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse as ChatCreateResponse;
use OpenAI\Responses\Meta\MetaInformation;
use OpenAI\Responses\Moderations\CreateResponse as ModerationCreateResponse;
use Tests\TestCase;

class SlugServiceTest extends TestCase
{
    private SlugService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SlugService();
    }

    public function test_moderate_slug_returns_true_for_clean_content(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ModerationCreateResponse::from([
            'id' => 'modr-123',
            'model' => 'omni-moderation-latest',
            'results' => [
                [
                    'flagged' => false,
                    'categories' => [
                        'harassment' => false,
                        'hate' => false,
                        'self-harm' => false,
                        'sexual' => false,
                        'violence' => false,
                    ],
                    'category_scores' => [
                        'harassment' => 0.001,
                        'hate' => 0.001,
                        'self-harm' => 0.001,
                        'sexual' => 0.001,
                        'violence' => 0.001,
                    ],
                ],
            ],
        ], $meta);

        OpenAI::fake([
            ModerationCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->moderateSlug('clean-content');

        $this->assertTrue($result);
    }

    public function test_moderate_slug_returns_false_for_flagged_content(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ModerationCreateResponse::from([
            'id' => 'modr-456',
            'model' => 'omni-moderation-latest',
            'results' => [
                [
                    'flagged' => true,
                    'categories' => [
                        'harassment' => true,
                        'hate' => false,
                        'self-harm' => false,
                        'sexual' => false,
                        'violence' => false,
                    ],
                    'category_scores' => [
                        'harassment' => 0.95,
                        'hate' => 0.01,
                        'self-harm' => 0.01,
                        'sexual' => 0.01,
                        'violence' => 0.01,
                    ],
                ],
            ],
        ], $meta);

        OpenAI::fake([
            ModerationCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->moderateSlug('inappropriate-content');

        $this->assertFalse($result);
    }

    public function test_moderate_slug_handles_missing_flagged_field(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ModerationCreateResponse::from([
            'id' => 'modr-789',
            'model' => 'omni-moderation-latest',
            'results' => [
                [
                    'flagged' => false,
                    'categories' => [],
                    'category_scores' => [],
                ],
            ],
        ], $meta);

        OpenAI::fake([
            ModerationCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->moderateSlug('test-slug');

        $this->assertTrue($result);
    }

    public function test_translate_slug_all_languages_returns_translations(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ChatCreateResponse::from([
            'id' => 'chatcmpl-123',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => 'gpt-4o-mini',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => json_encode([
                            'en' => 'Test Keyword',
                            'vi' => 'Từ khóa kiểm tra',
                        ]),
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 50,
                'completion_tokens' => 20,
                'total_tokens' => 70,
            ],
        ], $meta);

        OpenAI::fake([
            ChatCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->translateSlugAllLanguages('test-keyword');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertArrayHasKey('vi', $result);
        $this->assertEquals('Test Keyword', $result['en']);
        $this->assertEquals('Từ khóa kiểm tra', $result['vi']);
    }

    public function test_translate_slug_all_languages_handles_empty_content(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ChatCreateResponse::from([
            'id' => 'chatcmpl-456',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => 'gpt-4o-mini',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => '',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 50,
                'completion_tokens' => 0,
                'total_tokens' => 50,
            ],
        ], $meta);

        OpenAI::fake([
            ChatCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->translateSlugAllLanguages('test');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_translate_slug_all_languages_handles_invalid_json(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ChatCreateResponse::from([
            'id' => 'chatcmpl-789',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => 'gpt-4o-mini',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Invalid JSON response',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 50,
                'completion_tokens' => 5,
                'total_tokens' => 55,
            ],
        ], $meta);

        OpenAI::fake([
            ChatCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->translateSlugAllLanguages('test');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_translate_slug_all_languages_handles_null_response(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ChatCreateResponse::from([
            'id' => 'chatcmpl-null',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => 'gpt-4o-mini',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => null,
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 50,
                'completion_tokens' => 0,
                'total_tokens' => 50,
            ],
        ], $meta);

        OpenAI::fake([
            ChatCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->translateSlugAllLanguages('test');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_translate_slug_all_languages_includes_all_supported_languages(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ChatCreateResponse::from([
            'id' => 'chatcmpl-multi',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => 'gpt-4o-mini',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => json_encode([
                            'en' => 'Programming',
                            'vi' => 'Lập trình',
                        ]),
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 60,
                'completion_tokens' => 25,
                'total_tokens' => 85,
            ],
        ], $meta);

        OpenAI::fake([
            ChatCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->translateSlugAllLanguages('programming');

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('en', $result);
        $this->assertArrayHasKey('vi', $result);
    }

    public function test_moderate_slug_with_special_characters(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ModerationCreateResponse::from([
            'id' => 'modr-special',
            'model' => 'omni-moderation-latest',
            'results' => [
                [
                    'flagged' => false,
                    'categories' => [
                        'harassment' => false,
                        'hate' => false,
                        'self-harm' => false,
                        'sexual' => false,
                        'violence' => false,
                    ],
                    'category_scores' => [
                        'harassment' => 0.001,
                        'hate' => 0.001,
                        'self-harm' => 0.001,
                        'sexual' => 0.001,
                        'violence' => 0.001,
                    ],
                ],
            ],
        ], $meta);

        OpenAI::fake([
            ModerationCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->moderateSlug('test-slug-@#$%');

        $this->assertTrue($result);
    }

    public function test_translate_slug_preserves_slug_format(): void
    {
        $meta = MetaInformation::from([
            'x-request-id' => ['test-request-id'],
        ]);

        $mock_response = ChatCreateResponse::from([
            'id' => 'chatcmpl-slug',
            'object' => 'chat.completion',
            'created' => time(),
            'model' => 'gpt-4o-mini',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => json_encode([
                            'en' => 'web-development',
                            'vi' => 'phat-trien-web',
                        ]),
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 55,
                'completion_tokens' => 22,
                'total_tokens' => 77,
            ],
        ], $meta);

        OpenAI::fake([
            ChatCreateResponse::class => $mock_response,
        ]);

        $result = $this->service->translateSlugAllLanguages('web-development');

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        foreach ($result as $translation) {
            $this->assertIsString($translation);
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
