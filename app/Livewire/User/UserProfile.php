<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\TeacherReview;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserProfile extends Component
{
    public User $user;

    public bool $is_own_profile = false;

    public bool $is_teacher = false;

    public bool $is_student = false;

    /** @var array<string> */
    public array $teaching_languages = [];

    public int $review_rating = 5;

    public string $review_comment = '';

    public bool $show_review_form = false;

    public bool $can_review = false;

    public ?TeacherReview $existing_review = null;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->is_own_profile = Auth::id() === $user->id;
        $this->is_teacher = $user->isTeacher();
        $this->is_student = $user->isStudent();

        if ($this->is_teacher) {
            $this->teaching_languages = $user->getTeachingLanguageValues();
        }

        $this->checkCanReview();
    }

    protected function checkCanReview(): void
    {
        /** @var User|null $current_user */
        $current_user = Auth::user();

        if (! $current_user || $this->is_own_profile || ! $this->is_teacher) {
            $this->can_review = false;

            return;
        }

        // Only students can review teachers
        if (! $current_user->isStudent()) {
            $this->can_review = false;

            return;
        }

        $this->can_review = true;

        // Check for existing review
        $this->existing_review = TeacherReview::where('teacher_id', '=', $this->user->id)
            ->where('student_id', '=', $current_user->id)
            ->first();

        if ($this->existing_review) {
            $this->review_rating = $this->existing_review->rating;
            $this->review_comment = $this->existing_review->comment ?? '';
        }
    }

    public function toggleReviewForm(): void
    {
        $this->show_review_form = ! $this->show_review_form;
    }

    public function submitReview(): void
    {
        if (! $this->can_review) {
            return;
        }

        $this->validate([
            'review_rating' => 'required|integer|min:1|max:5',
            'review_comment' => 'nullable|string|max:500',
        ]);

        /** @var User $current_user */
        $current_user = Auth::user();

        TeacherReview::updateOrCreate(
            [
                'teacher_id' => $this->user->id,
                'student_id' => $current_user->id,
            ],
            [
                'rating' => $this->review_rating,
                'comment' => $this->review_comment ?: null,
            ]
        );

        $this->show_review_form = false;
        $this->user->refresh();
        $this->checkCanReview();

        session()->flash('review_message', __('profile.review_submitted'));
    }

    public function deleteReview(): void
    {
        if (! $this->existing_review) {
            return;
        }

        $this->existing_review->delete();
        $this->existing_review = null;
        $this->review_rating = 5;
        $this->review_comment = '';
        $this->user->refresh();

        session()->flash('review_message', __('profile.review_deleted'));
    }

    /**
     * @return Collection<int, TeacherReview>
     */
    public function getReviews(): Collection
    {
        return $this->user->receivedReviews()
            ->with('student')
            ->latest()
            ->take(10)
            ->get();
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.user.user-profile', [
            'reviews' => $this->is_teacher ? $this->getReviews() : collect(),
        ]);
    }
}
