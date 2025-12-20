<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\LanguageLevelEnum;
use App\Livewire\Lesson\LessonIndex;
use App\Models\Lesson\Lesson;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        app()->setLocale('en');
    }

    // ===== RENDERING TESTS =====

    public function test_component_can_render(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->assertOk();
    }

    public function test_component_displays_user_lessons(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'My Test Lesson',
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->assertSee('My Test Lesson');
    }

    public function test_component_does_not_display_other_users_lessons(): void
    {
        $other_user = User::factory()->create();
        Lesson::factory()->create([
            'user_id' => $other_user->id,
            'lesson_name' => 'Other User Lesson',
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->assertDontSee('Other User Lesson');
    }

    public function test_component_paginates_lessons(): void
    {
        // Create 15 lessons (more than page size of 12)
        Lesson::factory()->count(15)->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(LessonIndex::class);

        // Should see pagination
        $lessons = $component->viewData('lessons');
        $this->assertSame(15, $lessons->total());
        $this->assertSame(12, $lessons->perPage());
    }

    // ===== FILTER TESTS =====

    public function test_search_filter_works(): void
    {
        Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'English Grammar',
        ]);
        Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'Vietnamese Vocabulary',
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('temp_search', 'Grammar')
            ->call('applyFilters')
            ->assertSee('English Grammar')
            ->assertDontSee('Vietnamese Vocabulary');
    }

    public function test_language_level_filter_works(): void
    {
        Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'Beginner Lesson',
            'language_level' => LanguageLevelEnum::A1->value,
        ]);
        Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'Advanced Lesson',
            'language_level' => LanguageLevelEnum::C2->value,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('temp_language_levels', [LanguageLevelEnum::A1->value])
            ->call('applyFilters')
            ->assertSee('Beginner Lesson')
            ->assertDontSee('Advanced Lesson');
    }

    public function test_sort_by_newest_works(): void
    {
        $old_lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'Old Lesson',
            'created_at' => now()->subDays(5),
        ]);
        $new_lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'New Lesson',
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('sort', 'newest');

        $lessons = $component->viewData('lessons');
        $this->assertSame($new_lesson->id, $lessons->first()->id);
    }

    public function test_sort_by_oldest_works(): void
    {
        $old_lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'Old Lesson',
            'created_at' => now()->subDays(5),
        ]);
        $new_lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
            'lesson_name' => 'New Lesson',
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('sort', 'oldest');

        $lessons = $component->viewData('lessons');
        $this->assertSame($old_lesson->id, $lessons->first()->id);
    }

    public function test_reset_filters_clears_all_filters(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('temp_search', 'test')
            ->set('temp_language_levels', [LanguageLevelEnum::A1->value])
            ->call('applyFilters')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('language_levels', []);
    }

    public function test_filters_persist_in_session(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('temp_search', 'grammar')
            ->set('temp_language_levels', [LanguageLevelEnum::B1->value])
            ->call('applyFilters');

        $this->assertSame('grammar', session('lesson_filters.search'));
        $this->assertSame([LanguageLevelEnum::B1->value], session('lesson_filters.language_levels'));
    }

    // ===== DELETE TESTS =====

    public function test_can_delete_own_lesson(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('confirmDelete', $lesson->id)
            ->assertSet('lesson_to_delete', $lesson->id)
            ->assertSet('show_delete_confirm', true)
            ->call('deleteLesson')
            ->assertSet('show_delete_confirm', false);

        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    public function test_cannot_delete_other_users_lesson(): void
    {
        $other_user = User::factory()->create();
        $lesson = Lesson::factory()->create([
            'user_id' => $other_user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('confirmDelete', $lesson->id)
            ->call('deleteLesson');

        $this->assertDatabaseHas('lessons', ['id' => $lesson->id]);
    }

    public function test_cancel_delete_closes_modal(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('confirmDelete', $lesson->id)
            ->assertSet('show_delete_confirm', true)
            ->call('cancelDelete')
            ->assertSet('show_delete_confirm', false)
            ->assertSet('lesson_to_delete', null);
    }

    // ===== MODAL TESTS =====

    public function test_can_open_manage_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('openManageModal')
            ->assertSet('show_manage_modal', true)
            ->assertDispatched('reset-manage-lesson-state');
    }

    public function test_can_close_manage_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('openManageModal')
            ->call('closeManageModal')
            ->assertSet('show_manage_modal', false);
    }

    // ===== DROPDOWN TESTS =====

    public function test_can_toggle_dropdown(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('toggleDropdown', $lesson->id)
            ->assertSet('open_dropdowns.'.$lesson->id, true)
            ->call('toggleDropdown', $lesson->id)
            ->assertSet('open_dropdowns.'.$lesson->id, false);
    }

    public function test_opening_dropdown_closes_others(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->user->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('toggleDropdown', $lesson1->id)
            ->assertSet('open_dropdowns.'.$lesson1->id, true)
            ->call('toggleDropdown', $lesson2->id)
            ->assertSet('open_dropdowns.'.$lesson1->id, false)
            ->assertSet('open_dropdowns.'.$lesson2->id, true);
    }

    public function test_can_close_dropdown_explicitly(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('toggleDropdown', $lesson->id)
            ->call('closeDropdown', $lesson->id)
            ->assertSet('open_dropdowns.'.$lesson->id, false);
    }

    // ===== PLACEHOLDER TESTS =====

    public function test_can_add_placeholder(): void
    {
        $this->markTestSkipped('lesson-creation-queued event handler was removed from LessonIndex');
    }

    public function test_refresh_lessons_clears_placeholders(): void
    {
        $this->markTestSkipped('lesson-creation-queued event handler was removed from LessonIndex');
    }

    // ===== FILTER UI STATE TESTS =====

    public function test_can_toggle_filters_panel(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->assertSet('show_filters', false)
            ->call('toggleFilters')
            ->assertSet('show_filters', true)
            ->call('toggleFilters')
            ->assertSet('show_filters', false);
    }

    public function test_opening_filters_syncs_temp_values(): void
    {
        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('search', 'test')
            ->set('language_levels', [LanguageLevelEnum::A2->value])
            ->call('toggleFilters')
            ->assertSet('temp_search', 'test')
            ->assertSet('temp_language_levels', [LanguageLevelEnum::A2->value]);
    }

    public function test_has_active_filters_returns_correct_value(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(LessonIndex::class);

        $this->assertFalse($component->instance()->hasActiveFilters());

        $component->set('search', 'test');
        $this->assertTrue($component->instance()->hasActiveFilters());
    }

    // ===== REGENERATE TESTS =====

    public function test_can_confirm_regenerate(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('confirmRegenerate', $lesson->id)
            ->assertSet('lesson_to_regenerate', $lesson->id)
            ->assertSet('show_regenerate_confirm', true);
    }

    public function test_can_cancel_regenerate(): void
    {
        $lesson = Lesson::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->call('confirmRegenerate', $lesson->id)
            ->call('cancelRegenerate')
            ->assertSet('show_regenerate_confirm', false)
            ->assertSet('lesson_to_regenerate', null);
    }

    // ===== HELPER METHOD TESTS =====

    public function test_get_language_levels_returns_all_levels(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(LessonIndex::class);

        $levels = $component->instance()->getLanguageLevels();

        $this->assertCount(6, $levels); // A1, A2, B1, B2, C1, C2
        $this->assertSame(LanguageLevelEnum::A1->value, $levels[0]['value']);
    }

    public function test_filtered_lessons_count_computed_property(): void
    {
        Lesson::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'language_level' => LanguageLevelEnum::A1->value,
        ]);
        Lesson::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'language_level' => LanguageLevelEnum::B2->value,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(LessonIndex::class)
            ->set('temp_language_levels', [LanguageLevelEnum::A1->value]);

        $this->assertSame(5, $component->instance()->filteredLessonsCount());
    }
}
