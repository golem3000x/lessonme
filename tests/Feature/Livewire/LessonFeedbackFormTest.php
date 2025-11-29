<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Lesson\LessonFeedbackForm;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonFeedbackFormTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertStatus(200)
            ->assertViewIs('livewire.lesson.lesson-feedback-form');
    }

    public function test_mount_sets_lesson_id(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertSet('lesson_id', $this->lesson->id);
    }

    public function test_mount_sets_already_submitted_to_false_when_no_feedback(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertSet('already_submitted', false);
    }

    public function test_mount_sets_already_submitted_to_true_when_feedback_exists(): void
    {
        LessonFeedback::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertSet('already_submitted', true);
    }

    public function test_component_initializes_with_closed_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertSet('show_modal', false);
    }

    public function test_component_initializes_with_null_rating(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertSet('rating', null);
    }

    public function test_component_initializes_with_empty_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->assertSet('comment', '');
    }

    public function test_open_modal_sets_show_modal_to_true(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->call('openModal')
            ->assertSet('show_modal', true);
    }

    public function test_open_modal_resets_rating_and_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', 'Previous comment')
            ->call('openModal')
            ->assertSet('rating', null)
            ->assertSet('comment', '');
    }

    public function test_open_modal_does_nothing_when_already_submitted(): void
    {
        LessonFeedback::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->call('openModal')
            ->assertSet('show_modal', false);
    }

    public function test_close_modal_sets_show_modal_to_false(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('show_modal', true)
            ->call('closeModal')
            ->assertSet('show_modal', false);
    }

    public function test_close_modal_resets_rating_and_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', 'Test comment')
            ->call('closeModal')
            ->assertSet('rating', null)
            ->assertSet('comment', '');
    }

    public function test_set_rating_updates_rating(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->call('setRating', 'like')
            ->assertSet('rating', 'like');
    }

    public function test_submit_feedback_creates_feedback_in_database(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', 'Great lesson!')
            ->call('submitFeedback');

        $this->assertDatabaseHas('lesson_feedbacks', [
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->user->id,
            'rating' => 'like',
            'comment' => 'Great lesson!',
        ]);
    }

    public function test_submit_feedback_validates_required_rating(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', null)
            ->call('submitFeedback')
            ->assertHasErrors(['rating' => 'required']);
    }

    public function test_submit_feedback_validates_rating_values(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'invalid')
            ->call('submitFeedback')
            ->assertHasErrors(['rating' => 'in']);
    }

    public function test_submit_feedback_validates_comment_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', str_repeat('a', 501))
            ->call('submitFeedback')
            ->assertHasErrors(['comment' => 'max']);
    }

    public function test_submit_feedback_allows_like_rating(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback')
            ->assertHasNoErrors();
    }

    public function test_submit_feedback_allows_dislike_rating(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'dislike')
            ->call('submitFeedback')
            ->assertHasNoErrors();
    }

    public function test_submit_feedback_trims_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', '  Test comment  ')
            ->call('submitFeedback');

        $this->assertDatabaseHas('lesson_feedbacks', [
            'comment' => 'Test comment',
        ]);
    }

    public function test_submit_feedback_stores_null_for_empty_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', '')
            ->call('submitFeedback');

        $this->assertDatabaseHas('lesson_feedbacks', [
            'lesson_id' => $this->lesson->id,
            'comment' => null,
        ]);
    }

    public function test_submit_feedback_stores_null_for_whitespace_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', '   ')
            ->call('submitFeedback');

        $this->assertDatabaseHas('lesson_feedbacks', [
            'lesson_id' => $this->lesson->id,
            'comment' => null,
        ]);
    }

    public function test_submit_feedback_sets_already_submitted_to_true(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback')
            ->assertSet('already_submitted', true);
    }

    public function test_submit_feedback_closes_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback')
            ->assertSet('show_modal', false);
    }

    public function test_submit_feedback_dispatches_feedback_submitted_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback')
            ->assertDispatched('feedback-submitted');
    }

    public function test_submit_feedback_does_nothing_when_already_submitted(): void
    {
        LessonFeedback::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback');

        // Should still have only one feedback
        $this->assertEquals(1, LessonFeedback::where('lesson_id', '=', $this->lesson->id)->count());
    }

    public function test_submit_feedback_returns_early_for_non_existent_lesson(): void
    {
        $non_existent_lesson_id = 99999;

        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $non_existent_lesson_id])
            ->set('rating', 'like')
            ->call('submitFeedback');

        $this->assertEquals(0, LessonFeedback::count());
    }

    public function test_submit_feedback_returns_early_for_guest(): void
    {
        Livewire::test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback');

        $this->assertEquals(0, LessonFeedback::count());
    }

    public function test_multiple_users_can_submit_feedback_for_same_lesson(): void
    {
        $another_user = User::factory()->create();

        // First user
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback');

        // Second user
        Livewire::actingAs($another_user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'dislike')
            ->call('submitFeedback');

        $this->assertEquals(2, LessonFeedback::where('lesson_id', '=', $this->lesson->id)->count());
    }

    public function test_user_can_submit_feedback_for_different_lessons(): void
    {
        $another_lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // First lesson
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->call('submitFeedback');

        // Second lesson
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $another_lesson->id])
            ->set('rating', 'dislike')
            ->call('submitFeedback');

        $this->assertEquals(2, LessonFeedback::where('user_id', '=', $this->user->id)->count());
    }

    public function test_comment_is_optional(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('rating', 'like')
            ->set('comment', '')
            ->call('submitFeedback')
            ->assertHasNoErrors();
    }

    public function test_feedback_form_state_persists_after_validation_error(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(LessonFeedbackForm::class, ['lesson_id' => $this->lesson->id])
            ->set('comment', 'This is a test comment')
            ->call('submitFeedback')
            ->assertHasErrors('rating');

        // Comment should still be there
        $component->assertSet('comment', 'This is a test comment');
    }
}
