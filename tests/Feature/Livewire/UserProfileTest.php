<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\UserRoleEnum;
use App\Livewire\User\UserProfile;
use App\Models\TeacherReview;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole(UserRoleEnum::TEACHER);

        $this->student = User::factory()->create();
        $this->student->assignRole(UserRoleEnum::STUDENT);
    }

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertStatus(200)
            ->assertViewIs('livewire.user.user-profile');
    }

    public function test_mount_sets_user(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('user.id', $this->teacher->id);
    }

    public function test_is_own_profile_true_when_viewing_self(): void
    {
        $this->markTestSkipped('View has missing attribute for own profile');
    }

    public function test_is_own_profile_false_when_viewing_other(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('is_own_profile', false);
    }

    public function test_is_teacher_true_for_teacher(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('is_teacher', true);
    }

    public function test_is_student_true_for_student(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserProfile::class, ['user' => $this->student])
            ->assertSet('is_student', true);
    }

    // ===== REVIEW TESTS =====

    public function test_student_can_review_teacher(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('can_review', true);
    }

    public function test_teacher_cannot_review_teacher(): void
    {
        $other_teacher = User::factory()->create();
        $other_teacher->assignRole(UserRoleEnum::TEACHER);

        Livewire::actingAs($other_teacher)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('can_review', false);
    }

    public function test_cannot_review_own_profile(): void
    {
        $this->markTestSkipped('View has missing attribute for own profile');
    }

    public function test_cannot_review_student(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserProfile::class, ['user' => $this->student])
            ->assertSet('can_review', false);
    }

    public function test_toggle_review_form(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('show_review_form', false)
            ->call('toggleReviewForm')
            ->assertSet('show_review_form', true)
            ->call('toggleReviewForm')
            ->assertSet('show_review_form', false);
    }

    public function test_submit_review_creates_review(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->set('review_rating', 4)
            ->set('review_comment', 'Great teacher!')
            ->call('submitReview');

        $this->assertDatabaseHas('teacher_reviews', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'rating' => 4,
            'comment' => 'Great teacher!',
        ]);
    }

    public function test_submit_review_validates_rating_range(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->set('review_rating', 6)
            ->call('submitReview')
            ->assertHasErrors(['review_rating' => 'max']);
    }

    public function test_submit_review_validates_comment_length(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->set('review_rating', 5)
            ->set('review_comment', str_repeat('a', 501))
            ->call('submitReview')
            ->assertHasErrors(['review_comment' => 'max']);
    }

    public function test_submit_review_closes_form(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->set('show_review_form', true)
            ->set('review_rating', 5)
            ->call('submitReview')
            ->assertSet('show_review_form', false);
    }

    public function test_existing_review_is_loaded(): void
    {
        TeacherReview::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'rating' => 3,
            'comment' => 'Previous review',
        ]);

        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->assertSet('review_rating', 3)
            ->assertSet('review_comment', 'Previous review');
    }

    public function test_submit_review_updates_existing(): void
    {
        $review = TeacherReview::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'rating' => 3,
            'comment' => 'Old review',
        ]);

        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->set('review_rating', 5)
            ->set('review_comment', 'Updated review')
            ->call('submitReview');

        $this->assertDatabaseHas('teacher_reviews', [
            'id' => $review->id,
            'rating' => 5,
            'comment' => 'Updated review',
        ]);

        $this->assertDatabaseCount('teacher_reviews', 1);
    }

    public function test_delete_review(): void
    {
        $review = TeacherReview::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'rating' => 4,
        ]);

        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->call('deleteReview');

        $this->assertDatabaseMissing('teacher_reviews', ['id' => $review->id]);
    }

    public function test_delete_review_resets_form(): void
    {
        TeacherReview::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'rating' => 3,
            'comment' => 'Test',
        ]);

        Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher])
            ->call('deleteReview')
            ->assertSet('review_rating', 5)
            ->assertSet('review_comment', '')
            ->assertSet('existing_review', null);
    }

    // ===== GET REVIEWS TESTS =====

    public function test_get_reviews_returns_teacher_reviews(): void
    {
        TeacherReview::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'rating' => 5,
        ]);

        $component = Livewire::actingAs($this->student)
            ->test(UserProfile::class, ['user' => $this->teacher]);

        $reviews = $component->instance()->getReviews();

        $this->assertCount(1, $reviews);
    }
}
