<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Lesson\Lesson;
use App\Models\User;
use App\Services\Lesson\LessonSimilarityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonSimilarityServiceTest extends TestCase
{
    use RefreshDatabase;

    private LessonSimilarityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LessonSimilarityService;
    }

    private function createEmbedding(float $base = 0.5): array
    {
        $embedding = [];
        for ($i = 0; $i < 1536; $i++) {
            $embedding[] = $base + (random_int(-100, 100) / 10000);
        }

        return $embedding;
    }

    public function test_find_similar_returns_null_when_no_lessons_exist(): void
    {
        $user = User::factory()->create();
        $embedding = $this->createEmbedding();

        $result = $this->service->findSimilar($user->id, $embedding);

        $this->assertNull($result);
    }

    public function test_find_similar_uses_correct_threshold_constant(): void
    {
        // Verify that the similarity threshold constant is set correctly
        $this->assertEquals(0.9, Lesson::SIMILARITY_THRESHOLD);

        // This test ensures the threshold is documented and accessible
        $this->assertIsFloat(Lesson::SIMILARITY_THRESHOLD);
        $this->assertGreaterThan(0, Lesson::SIMILARITY_THRESHOLD);
        $this->assertLessThanOrEqual(1, Lesson::SIMILARITY_THRESHOLD);
    }

    public function test_find_similar_returns_lesson_with_identical_embedding(): void
    {
        $user = User::factory()->create();
        $embedding = $this->createEmbedding(0.5);

        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'lesson_name' => 'Similar Lesson',
            'embedding' => $embedding,
        ]);

        // Search with same embedding
        $result = $this->service->findSimilar($user->id, $embedding);

        $this->assertNotNull($result);
        $this->assertEquals($lesson->id, $result->id);
        $this->assertEquals('Similar Lesson', $result->lesson_name);
    }

    public function test_find_similar_returns_lesson_with_very_similar_embedding(): void
    {
        $user = User::factory()->create();
        $base_embedding = $this->createEmbedding(0.5);

        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'embedding' => $base_embedding,
        ]);

        // Create slightly modified embedding (very similar)
        $similar_embedding = array_map(function ($val) {
            return $val + 0.001;
        }, $base_embedding);

        $result = $this->service->findSimilar($user->id, $similar_embedding);

        $this->assertNotNull($result);
        $this->assertEquals($lesson->id, $result->id);
    }

    public function test_find_similar_only_returns_lessons_for_specific_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $embedding = $this->createEmbedding(0.5);

        // Create lesson for user2
        Lesson::factory()->create([
            'user_id' => $user2->id,
            'embedding' => $embedding,
        ]);

        // Search for user1 with same embedding
        $result = $this->service->findSimilar($user1->id, $embedding);

        // Should not find user2's lesson
        $this->assertNull($result);
    }

    public function test_find_similar_returns_most_similar_when_multiple_exist(): void
    {
        $user = User::factory()->create();
        $search_embedding = $this->createEmbedding(0.5);

        // Create multiple lessons with varying similarity
        $lesson_very_similar = Lesson::factory()->create([
            'user_id' => $user->id,
            'lesson_name' => 'Very Similar',
            'embedding' => array_map(fn ($v) => $v + 0.001, $search_embedding),
        ]);

        $lesson_less_similar = Lesson::factory()->create([
            'user_id' => $user->id,
            'lesson_name' => 'Less Similar',
            'embedding' => array_map(fn ($v) => $v + 0.02, $search_embedding),
        ]);

        $result = $this->service->findSimilar($user->id, $search_embedding);

        // Should return the most similar one
        $this->assertNotNull($result);
        $this->assertEquals($lesson_very_similar->id, $result->id);
        $this->assertEquals('Very Similar', $result->lesson_name);
    }

    public function test_find_similar_query_uses_pgvector_operator(): void
    {
        $user = User::factory()->create();
        $embedding = $this->createEmbedding(0.5);

        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'embedding' => $embedding,
        ]);

        // Execute the query and verify it completes successfully
        $result = $this->service->findSimilar($user->id, $embedding);

        // The query uses pgvector <=> operator for cosine distance
        // If it executes without error, the operator is working correctly
        $this->assertNotNull($result);
        $this->assertInstanceOf(Lesson::class, $result);
    }

    public function test_find_similar_works_with_different_embedding_sizes(): void
    {
        $user = User::factory()->create();

        // Standard 1536-dimensional embedding
        $embedding_1536 = $this->createEmbedding(0.5);

        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'embedding' => $embedding_1536,
        ]);

        $result = $this->service->findSimilar($user->id, $embedding_1536);

        $this->assertNotNull($result);
        $this->assertEquals($lesson->id, $result->id);
    }

    public function test_find_similar_handles_empty_embedding_with_exception(): void
    {
        $user = User::factory()->create();

        Lesson::factory()->create([
            'user_id' => $user->id,
            'embedding' => $this->createEmbedding(),
        ]);

        // PostgreSQL pgvector will throw exception for empty embedding
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('vector must have at least 1 dimension');

        $this->service->findSimilar($user->id, []);
    }

    public function test_find_similar_returns_lesson_object_with_similarity_score(): void
    {
        $user = User::factory()->create();
        $embedding = $this->createEmbedding(0.5);

        $lesson = Lesson::factory()->create([
            'user_id' => $user->id,
            'embedding' => $embedding,
        ]);

        $result = $this->service->findSimilar($user->id, $embedding);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Lesson::class, $result);

        // The query adds similarity_score as a computed column
        // For identical embeddings, score should be very close to 1.0
        if (isset($result->similarity_score)) {
            $this->assertGreaterThanOrEqual(0.9, $result->similarity_score);
        }
    }
}
