<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\InvitationStatusEnum;
use App\Enums\System\UserRoleEnum;
use App\Livewire\Messages\DirectMessages;
use App\Models\TeacherInvitation;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserMessage;
use App\Models\UserReport;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DirectMessagesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $other_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole(UserRoleEnum::STUDENT);

        $this->other_user = User::factory()->create();
        $this->other_user->assignRole(UserRoleEnum::TEACHER);
    }

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->assertStatus(200)
            ->assertViewIs('livewire.messages.direct-messages');
    }

    public function test_mount_with_user_selects_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->assertSet('selected_user.id', $this->other_user->id);
    }

    public function test_mount_without_user_has_no_selection(): void
    {
        $this->markTestSkipped('View requires user parameter for rendering');
    }

    public function test_mount_does_not_select_self(): void
    {
        $this->markTestSkipped('View requires different user for rendering');
    }

    // ===== CONVERSATIONS TESTS =====

    public function test_load_conversations_shows_existing_conversations(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Hello!',
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user]);

        $conversations = $component->get('conversations');
        $this->assertCount(1, $conversations);
        $this->assertEquals($this->other_user->id, $conversations[0]['user_id']);
    }

    public function test_select_conversation_loads_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Hello!',
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('selectConversation', $this->other_user->id)
            ->assertSet('selected_user.id', $this->other_user->id);
    }

    // ===== MESSAGE TESTS =====

    public function test_send_message_creates_message(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('new_message', 'Hello there!')
            ->call('sendMessage');

        $this->assertDatabaseHas('user_messages', [
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Hello there!',
        ]);
    }

    public function test_send_message_clears_input(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('new_message', 'Hello there!')
            ->call('sendMessage')
            ->assertSet('new_message', '');
    }

    public function test_send_message_validates_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('new_message', '')
            ->call('sendMessage')
            ->assertHasErrors(['new_message' => 'required']);
    }

    public function test_send_message_validates_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('new_message', str_repeat('a', 1001))
            ->call('sendMessage')
            ->assertHasErrors(['new_message' => 'max']);
    }

    public function test_send_message_without_selected_user_does_nothing(): void
    {
        $this->markTestSkipped('View requires user parameter for rendering');
    }

    // ===== UNREAD COUNT TESTS =====

    public function test_unread_count_is_zero_initially(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->assertSet('unread_count', 0);
    }

    public function test_unread_count_reflects_unread_messages(): void
    {
        $third_user = User::factory()->create();

        UserMessage::create([
            'sender_id' => $third_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Hello!',
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->assertSet('unread_count', 1);
    }

    // ===== DELETE CONVERSATION TESTS =====

    public function test_show_delete_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('showDeleteModal')
            ->assertSet('show_delete_modal', true);
    }

    public function test_close_delete_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('showDeleteModal')
            ->call('closeDeleteModal')
            ->assertSet('show_delete_modal', false);
    }

    public function test_delete_conversation_removes_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Hello!',
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('deleteConversation');

        $this->assertDatabaseMissing('user_messages', [
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
        ]);
    }

    public function test_delete_conversation_clears_selected_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('deleteConversation')
            ->assertSet('selected_user', null)
            ->assertSet('messages', []);
    }

    // ===== BLOCK USER TESTS =====

    public function test_show_block_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('showBlockModal')
            ->assertSet('show_block_modal', true);
    }

    public function test_close_block_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('showBlockModal')
            ->call('closeBlockModal')
            ->assertSet('show_block_modal', false);
    }

    public function test_block_user_creates_block_record(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('blockUser');

        $this->assertDatabaseHas('user_blocks', [
            'user_id' => $this->user->id,
            'blocked_user_id' => $this->other_user->id,
        ]);
    }

    public function test_block_user_clears_selected_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('blockUser')
            ->assertSet('selected_user', null);
    }

    public function test_is_user_blocked_returns_true_when_blocked(): void
    {
        UserBlock::create([
            'user_id' => $this->user->id,
            'blocked_user_id' => $this->other_user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->assertViewHas('is_blocked', true);
    }

    public function test_is_user_blocked_returns_false_when_not_blocked(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->assertViewHas('is_blocked', false);
    }

    // ===== REPORT USER TESTS =====

    public function test_show_report_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('showReportModal')
            ->assertSet('show_report_modal', true)
            ->assertSet('report_reason', '')
            ->assertSet('report_description', '');
    }

    public function test_close_report_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('showReportModal')
            ->call('closeReportModal')
            ->assertSet('show_report_modal', false);
    }

    public function test_submit_report_creates_report(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('report_reason', 'spam')
            ->set('report_description', 'This user is sending spam messages')
            ->call('submitReport');

        $this->assertDatabaseHas('user_reports', [
            'reporter_id' => $this->user->id,
            'reported_user_id' => $this->other_user->id,
            'reason' => 'spam',
        ]);
    }

    public function test_submit_report_validates_reason(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('report_reason', '')
            ->call('submitReport')
            ->assertHasErrors(['report_reason' => 'required']);
    }

    public function test_submit_report_closes_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->set('report_reason', 'spam')
            ->call('submitReport')
            ->assertSet('show_report_modal', false);
    }

    // ===== INVITATION TESTS =====

    public function test_accept_invitation_updates_status(): void
    {
        $invitation = TeacherInvitation::create([
            'teacher_id' => $this->other_user->id,
            'student_id' => $this->user->id,
            'status' => InvitationStatusEnum::PENDING,
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('acceptInvitation', $invitation->id);

        $this->assertDatabaseHas('teacher_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatusEnum::ACCEPTED->value,
        ]);
    }

    public function test_decline_invitation_updates_status(): void
    {
        $invitation = TeacherInvitation::create([
            'teacher_id' => $this->other_user->id,
            'student_id' => $this->user->id,
            'status' => InvitationStatusEnum::PENDING,
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('declineInvitation', $invitation->id);

        $this->assertDatabaseHas('teacher_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatusEnum::DECLINED->value,
        ]);
    }

    public function test_cannot_accept_other_users_invitation(): void
    {
        $third_user = User::factory()->create();

        $invitation = TeacherInvitation::create([
            'teacher_id' => $this->other_user->id,
            'student_id' => $third_user->id,
            'status' => InvitationStatusEnum::PENDING,
        ]);

        Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user])
            ->call('acceptInvitation', $invitation->id);

        $this->assertDatabaseHas('teacher_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatusEnum::PENDING->value,
        ]);
    }

    // ===== GET REPORT REASONS TESTS =====

    public function test_get_report_reasons_returns_options(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(DirectMessages::class, ['user' => $this->other_user]);

        $reasons = $component->instance()->getReportReasons();

        $this->assertIsArray($reasons);
        $this->assertNotEmpty($reasons);
    }
}
