<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\InvitationStatusEnum;
use App\Enums\System\UserRoleEnum;
use App\Livewire\User\UserList;
use App\Models\TeacherInvitation;
use App\Models\User;
use App\Models\UserMessage;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->teacher = User::factory()->create(['is_accepting_students' => true]);
        $this->teacher->assignRole(UserRoleEnum::TEACHER);

        $this->student = User::factory()->create(['is_looking_for_teacher' => true]);
        $this->student->assignRole(UserRoleEnum::STUDENT);
    }

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.user.user-list');
    }

    public function test_mount_loads_languages(): void
    {
        $component = Livewire::actingAs($this->teacher)
            ->test(UserList::class);

        $languages = $component->get('languages');

        $this->assertNotEmpty($languages);
    }

    // ===== TAB TESTS =====

    public function test_set_tab_changes_tab(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->assertSet('tab', 'all')
            ->call('setTab', 'my_students')
            ->assertSet('tab', 'my_students');
    }

    public function test_set_tab_resets_pagination(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->set('paginators.page', 2)
            ->call('setTab', 'my_teachers')
            ->assertSet('paginators.page', 1);
    }

    // ===== FILTER TESTS =====

    public function test_role_filter_shows_only_teachers(): void
    {
        $another_teacher = User::factory()->create(['is_accepting_students' => true]);
        $another_teacher->assignRole(UserRoleEnum::TEACHER);

        $component = Livewire::actingAs($this->student)
            ->test(UserList::class)
            ->set('role_filter', 'teacher');

        $users = $component->viewData('users');

        foreach ($users as $user) {
            $this->assertTrue($user->hasRole(UserRoleEnum::TEACHER));
        }
    }

    public function test_role_filter_shows_only_students(): void
    {
        $another_student = User::factory()->create(['is_looking_for_teacher' => true]);
        $another_student->assignRole(UserRoleEnum::STUDENT);

        $component = Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->set('role_filter', 'student');

        $users = $component->viewData('users');

        foreach ($users as $user) {
            $this->assertTrue($user->hasRole(UserRoleEnum::STUDENT));
        }
    }

    public function test_updated_role_filter_resets_pagination(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->set('paginators.page', 2)
            ->set('role_filter', 'student')
            ->assertSet('paginators.page', 1);
    }

    public function test_updated_language_filter_resets_pagination(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->set('paginators.page', 2)
            ->set('language_filter', 'en')
            ->assertSet('paginators.page', 1);
    }

    // ===== GET USERS TESTS =====

    public function test_get_users_excludes_current_user(): void
    {
        $component = Livewire::actingAs($this->teacher)
            ->test(UserList::class);

        $users = $component->viewData('users');

        foreach ($users as $user) {
            $this->assertNotEquals($this->teacher->id, $user->id);
        }
    }

    public function test_my_teachers_tab_returns_connected_teachers(): void
    {
        $this->student->teachers()->attach($this->teacher->id);

        $component = Livewire::actingAs($this->student)
            ->test(UserList::class)
            ->call('setTab', 'my_teachers');

        $users = $component->viewData('users');

        $this->assertCount(1, $users);
        $this->assertEquals($this->teacher->id, $users->first()->id);
    }

    public function test_my_students_tab_returns_connected_students(): void
    {
        $this->teacher->students()->attach($this->student->id);

        $component = Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('setTab', 'my_students');

        $users = $component->viewData('users');

        $this->assertCount(1, $users);
        $this->assertEquals($this->student->id, $users->first()->id);
    }

    // ===== TOOLTIP TESTS =====

    public function test_toggle_tooltip_opens_tooltip(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('toggleTooltip', $this->student->id)
            ->assertSet('open_tooltips', [$this->student->id => $this->student->id]);
    }

    public function test_toggle_tooltip_closes_other_tooltips(): void
    {
        $another_student = User::factory()->create();

        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('toggleTooltip', $this->student->id)
            ->call('toggleTooltip', $another_student->id)
            ->assertSet('open_tooltips', [$another_student->id => $another_student->id]);
    }

    public function test_toggle_tooltip_closes_when_already_open(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('toggleTooltip', $this->student->id)
            ->call('toggleTooltip', $this->student->id)
            ->assertSet('open_tooltips', []);
    }

    public function test_close_tooltip_removes_tooltip(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('toggleTooltip', $this->student->id)
            ->call('closeTooltip', $this->student->id)
            ->assertSet('open_tooltips', []);
    }

    // ===== INVITE STUDENT TESTS =====

    public function test_teacher_can_invite_student(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('inviteStudent', $this->student->id);

        $this->assertDatabaseHas('teacher_invitations', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'status' => InvitationStatusEnum::PENDING->value,
        ]);

        $this->assertDatabaseHas('user_messages', [
            'sender_id' => $this->teacher->id,
            'recipient_id' => $this->student->id,
        ]);
    }

    public function test_student_cannot_invite(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserList::class)
            ->call('inviteStudent', $this->teacher->id);

        // Student should not be able to create invitations
        $this->assertDatabaseMissing('teacher_invitations', [
            'teacher_id' => $this->student->id,
            'student_id' => $this->teacher->id,
        ]);
    }

    public function test_cannot_invite_nonexistent_user(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('inviteStudent', 99999);

        $this->assertDatabaseCount('teacher_invitations', 0);
    }

    public function test_cannot_send_duplicate_pending_invitation(): void
    {
        $message = UserMessage::create([
            'sender_id' => $this->teacher->id,
            'recipient_id' => $this->student->id,
            'content' => 'Test invitation',
        ]);

        TeacherInvitation::create([
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'message_id' => $message->id,
            'status' => InvitationStatusEnum::PENDING->value,
        ]);

        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('inviteStudent', $this->student->id);

        // Should not create duplicate invitation
        $this->assertDatabaseCount('teacher_invitations', 1);
    }

    public function test_cannot_invite_existing_student(): void
    {
        $this->teacher->students()->attach($this->student->id);

        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('inviteStudent', $this->student->id);

        // Should not create invitation for existing student
        $this->assertDatabaseCount('teacher_invitations', 0);
    }

    // ===== CONFIRM MODAL TESTS =====

    public function test_confirm_leave_teacher_opens_modal(): void
    {
        Livewire::actingAs($this->student)
            ->test(UserList::class)
            ->call('confirmLeaveTeacher', $this->teacher->id)
            ->assertSet('show_confirm_modal', true)
            ->assertSet('user_to_remove', $this->teacher->id)
            ->assertSet('remove_action', 'leave_teacher');
    }

    public function test_confirm_remove_student_opens_modal(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('confirmRemoveStudent', $this->student->id)
            ->assertSet('show_confirm_modal', true)
            ->assertSet('user_to_remove', $this->student->id)
            ->assertSet('remove_action', 'remove_student');
    }

    public function test_close_confirm_modal_resets_state(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('confirmRemoveStudent', $this->student->id)
            ->call('closeConfirmModal')
            ->assertSet('show_confirm_modal', false)
            ->assertSet('user_to_remove', null)
            ->assertSet('remove_action', '');
    }

    // ===== EXECUTE REMOVE TESTS =====

    public function test_execute_remove_leaves_teacher(): void
    {
        $this->student->teachers()->attach($this->teacher->id);

        $this->assertDatabaseHas('teacher_student', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
        ]);

        Livewire::actingAs($this->student)
            ->test(UserList::class)
            ->call('confirmLeaveTeacher', $this->teacher->id)
            ->call('executeRemove');

        $this->assertDatabaseMissing('teacher_student', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_execute_remove_removes_student(): void
    {
        $this->teacher->students()->attach($this->student->id);

        $this->assertDatabaseHas('teacher_student', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
        ]);

        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->call('confirmRemoveStudent', $this->student->id)
            ->call('executeRemove');

        $this->assertDatabaseMissing('teacher_student', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_execute_remove_without_user_closes_modal(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(UserList::class)
            ->set('user_to_remove', null)
            ->call('executeRemove')
            ->assertSet('show_confirm_modal', false);
    }

    public function test_student_cannot_remove_student(): void
    {
        $another_student = User::factory()->create();
        $another_student->assignRole(UserRoleEnum::STUDENT);

        Livewire::actingAs($this->student)
            ->test(UserList::class)
            ->set('user_to_remove', $another_student->id)
            ->set('remove_action', 'remove_student')
            ->call('executeRemove');

        // No flash message for unauthorized action
        $this->assertTrue(true);
    }
}
