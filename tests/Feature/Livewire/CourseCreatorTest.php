<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\UserRoleEnum;
use App\Livewire\Course\CourseCreator;
use App\Models\Course\Course;
use App\Models\Lesson\Lesson;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseCreatorTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole(UserRoleEnum::TEACHER);
    }

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.course.course-creator');
    }

    public function test_mount_loads_available_languages(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->assertSet('available_languages', function ($languages) {
                return is_array($languages) && count($languages) > 0;
            });
    }

    public function test_mount_sets_default_lesson_lang(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->assertSet('lesson_lang', function ($lang) {
                return ! empty($lang);
            });
    }

    public function test_mount_sets_default_passing_score(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->assertSet('passing_score', 70);
    }

    // ===== EDIT MODE TESTS =====

    public function test_mount_in_edit_mode_loads_course_data(): void
    {
        $course = Course::factory()->create([
            'user_id' => $this->teacher->id,
            'title' => 'Test Course',
            'description' => 'Test Description',
            'language_level' => LanguageLevelEnum::B1,
            'lesson_lang' => SystemLanguageEnum::ENGLISH,
            'passing_score' => 80,
        ]);

        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->assertSet('title', 'Test Course')
            ->assertSet('description', 'Test Description')
            ->assertSet('language_level', LanguageLevelEnum::B1->value)
            ->assertSet('lesson_lang', SystemLanguageEnum::ENGLISH->value)
            ->assertSet('passing_score', 80);
    }

    public function test_mount_does_not_load_other_users_course(): void
    {
        $other_user = User::factory()->create();
        $course = Course::factory()->create([
            'user_id' => $other_user->id,
            'title' => 'Other Course',
        ]);

        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->assertSet('title', '');
    }

    // ===== VALIDATION TESTS =====

    public function test_save_validates_required_title(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', '')
            ->set('lesson_lang', 'en')
            ->call('saveCourse')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_save_validates_title_max_length(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', str_repeat('a', 256))
            ->set('lesson_lang', 'en')
            ->call('saveCourse')
            ->assertHasErrors(['title' => 'max']);
    }

    public function test_save_validates_required_lesson_lang(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', 'Test')
            ->set('lesson_lang', '')
            ->call('saveCourse')
            ->assertHasErrors(['lesson_lang' => 'required']);
    }

    public function test_save_validates_passing_score_range(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', 'Test')
            ->set('lesson_lang', 'en')
            ->set('passing_score', 101)
            ->call('saveCourse')
            ->assertHasErrors(['passing_score' => 'max']);
    }

    public function test_save_validates_passing_score_min(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', 'Test')
            ->set('lesson_lang', 'en')
            ->set('passing_score', -1)
            ->call('saveCourse')
            ->assertHasErrors(['passing_score' => 'min']);
    }

    // ===== CREATE TESTS =====

    public function test_save_creates_new_course(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', 'New Course')
            ->set('description', 'Course Description')
            ->set('lesson_lang', 'en')
            ->set('language_level', LanguageLevelEnum::A1->value)
            ->set('passing_score', 75)
            ->call('saveCourse');

        $this->assertDatabaseHas('courses', [
            'user_id' => $this->teacher->id,
            'title' => 'New Course',
            'description' => 'Course Description',
            'lesson_lang' => 'en',
            'passing_score' => 75,
        ]);
    }

    public function test_save_redirects_to_courses_index(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class)
            ->set('title', 'New Course')
            ->set('lesson_lang', 'en')
            ->call('saveCourse')
            ->assertRedirect(route('courses.index'));
    }

    // ===== UPDATE TESTS =====

    public function test_save_updates_existing_course(): void
    {
        $course = Course::factory()->create([
            'user_id' => $this->teacher->id,
            'title' => 'Old Title',
        ]);

        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->set('title', 'Updated Title')
            ->set('description', 'Updated Description')
            ->call('saveCourse');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);
    }

    // ===== LESSON MANAGEMENT TESTS =====

    public function test_remove_lesson_from_course(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $course->lessons()->attach($lesson->id, ['order' => 1]);

        Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->call('removeLessonFromCourse', $lesson->id);

        $this->assertDatabaseMissing('course_lessons', [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_move_lesson_up(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $course->lessons()->attach($lesson1->id, ['order' => 1]);
        $course->lessons()->attach($lesson2->id, ['order' => 2]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->call('moveLessonUp', 1);

        $lessons = $component->get('course_lessons');
        $this->assertEquals($lesson2->id, $lessons[0]['id']);
        $this->assertEquals($lesson1->id, $lessons[1]['id']);
    }

    public function test_move_lesson_down(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $course->lessons()->attach($lesson1->id, ['order' => 1]);
        $course->lessons()->attach($lesson2->id, ['order' => 2]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->call('moveLessonDown', 0);

        $lessons = $component->get('course_lessons');
        $this->assertEquals($lesson2->id, $lessons[0]['id']);
        $this->assertEquals($lesson1->id, $lessons[1]['id']);
    }

    public function test_move_lesson_up_at_first_position_does_nothing(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $course->lessons()->attach($lesson1->id, ['order' => 1]);
        $course->lessons()->attach($lesson2->id, ['order' => 2]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->call('moveLessonUp', 0);

        $lessons = $component->get('course_lessons');
        $this->assertEquals($lesson1->id, $lessons[0]['id']);
        $this->assertEquals($lesson2->id, $lessons[1]['id']);
    }

    public function test_move_lesson_down_at_last_position_does_nothing(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $course->lessons()->attach($lesson1->id, ['order' => 1]);
        $course->lessons()->attach($lesson2->id, ['order' => 2]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CourseCreator::class, ['course' => $course])
            ->call('moveLessonDown', 1);

        $lessons = $component->get('course_lessons');
        $this->assertEquals($lesson1->id, $lessons[0]['id']);
        $this->assertEquals($lesson2->id, $lessons[1]['id']);
    }
}
