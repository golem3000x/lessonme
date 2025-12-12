<?php

declare(strict_types=1);

namespace App\Livewire\Course;

use App\Enums\System\SystemLanguageEnum;
use App\Models\Course\Course;
use App\Models\Lesson\Lesson;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CourseCreator extends Component
{
    public ?Course $course = null;

    public string $title = '';

    public string $description = '';

    public string $language_level = '';

    public string $lesson_lang = '';

    public int $passing_score = 70;

    /** @var array<int,array{value:string,label:string,flag:string}> */
    public array $available_languages = [];

    /** @var array<int,array{id:int,name:string,order:int}> */
    public array $course_lessons = [];

    public function mount(?Course $course = null): void
    {
        $user = Auth::user();

        // Load available languages
        $teaching_languages = $user->getTeachingLanguageValues();
        if (empty($teaching_languages)) {
            $teaching_languages = SystemLanguageEnum::values();
        }

        $this->available_languages = collect($teaching_languages)
            ->map(function (string $lang_value) {
                $enum = SystemLanguageEnum::tryFrom($lang_value);

                return $enum ? [
                    'value' => $enum->value,
                    'label' => $enum->label(),
                    'flag' => $enum->flag(),
                ] : null;
            })
            ->filter()
            ->values()
            ->toArray();

        if (! empty($this->available_languages)) {
            $this->lesson_lang = $this->available_languages[0]['value'];
        }

        // Edit mode
        if ($course && $course->exists && $course->user_id === $user->id) {
            $this->course = $course;
            $this->title = $course->title;
            $this->description = $course->description ?? '';
            $this->language_level = $course->language_level?->value ?? '';
            $this->lesson_lang = $course->lesson_lang?->value ?? $this->lesson_lang;
            $this->passing_score = $course->passing_score;

            // Load course lessons
            $this->course_lessons = $course->lessons()
                ->orderByPivot('order')
                ->get()
                ->map(fn (Lesson $l) => [
                    'id' => $l->id,
                    'name' => $l->lesson_name,
                    'order' => $l->pivot->order,
                ])
                ->toArray();
        }
    }

    public function saveCourse(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'lesson_lang' => 'required|string',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $user = Auth::user();

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'language_level' => $this->language_level ?: null,
            'lesson_lang' => $this->lesson_lang,
            'passing_score' => $this->passing_score,
        ];

        if ($this->course) {
            $this->course->update($data);
        } else {
            $this->course = Course::create([
                ...$data,
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
            ]);
        }

        session()->flash('message', __('lessons.course_saved'));
        $this->redirect(route('courses.index'), navigate: true);
    }

    public function removeLessonFromCourse(int $lesson_id): void
    {
        if (! $this->course) {
            return;
        }

        $this->course->lessons()->detach($lesson_id);

        // Reload lessons
        $this->course_lessons = $this->course->lessons()
            ->orderByPivot('order')
            ->get()
            ->map(fn (Lesson $l) => [
                'id' => $l->id,
                'name' => $l->lesson_name,
                'order' => $l->pivot->order,
            ])
            ->toArray();

        // Re-order remaining lessons
        $this->reorderLessons();
    }

    public function reorderLessons(): void
    {
        if (! $this->course) {
            return;
        }

        foreach ($this->course_lessons as $index => $lesson) {
            $this->course->lessons()->updateExistingPivot($lesson['id'], [
                'order' => $index + 1,
            ]);
        }
    }

    public function moveLessonUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->course_lessons[$index])) {
            return;
        }

        $temp = $this->course_lessons[$index - 1];
        $this->course_lessons[$index - 1] = $this->course_lessons[$index];
        $this->course_lessons[$index] = $temp;

        $this->reorderLessons();
    }

    public function moveLessonDown(int $index): void
    {
        if ($index >= count($this->course_lessons) - 1 || ! isset($this->course_lessons[$index])) {
            return;
        }

        $temp = $this->course_lessons[$index + 1];
        $this->course_lessons[$index + 1] = $this->course_lessons[$index];
        $this->course_lessons[$index] = $temp;

        $this->reorderLessons();
    }

    public function render(): View
    {
        return view('livewire.course.course-creator');
    }
}
