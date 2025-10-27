<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Livewire\Chat\ChatLesson;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ChatLessonTest extends TestCase
{
    public function test_build_keywords_for_topic_returns_empty_when_topic_is_null(): void
    {
        // Arrange
        $component = new ChatLesson;

        // Act
        $method = (new ReflectionClass($component))->getMethod('buildKeywordsForTopic');
        $method->setAccessible(true);
        /** @var array<int,array{id:int,label:string}> $result */
        $result = $method->invoke($component, null);

        // Assert
        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
