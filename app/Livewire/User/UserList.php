<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Enums\System\InvitationStatusEnum;
use App\Enums\System\MessageTypeEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Models\TeacherInvitation;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $tab = 'all';

    public string $role_filter = '';

    public string $language_filter = '';

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    /** @var array<int, int> */
    public array $open_tooltips = [];

    public bool $show_confirm_modal = false;

    public ?int $user_to_remove = null;

    public string $remove_action = '';

    public function mount(): void
    {
        $this->languages = SystemLanguageEnum::all();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatedLanguageFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<User>|Collection<int, User>
     */
    public function getUsers(): LengthAwarePaginator|Collection
    {
        /** @var User $current_user */
        $current_user = Auth::user();

        // My Teachers tab - students see their connected teachers
        if ($this->tab === 'my_teachers') {
            return $current_user->teachers()->with(['roles', 'teachingLanguages', 'learningLanguages'])->get();
        }

        // My Students tab - teachers see their connected students
        if ($this->tab === 'my_students') {
            return $current_user->students()->with(['roles', 'teachingLanguages', 'learningLanguages'])->get();
        }

        $query = User::query()
            ->with(['roles', 'teachingLanguages', 'learningLanguages'])
            ->where('id', '!=', $current_user->id)
            ->whereHas('roles')
            ->whereDoesntHave('roles', function (Builder $q): void {
                $q->where('name', '=', 'admin');
            });

        // Role filter
        if ($this->role_filter === 'teacher') {
            $query->where('is_accepting_students', '=', true)
                ->whereHas('roles', function (Builder $q): void {
                    $q->where('name', '=', 'teacher');
                });
        } elseif ($this->role_filter === 'student') {
            $query->where('is_looking_for_teacher', '=', true)
                ->whereHas('roles', function (Builder $q): void {
                    $q->where('name', '=', 'student');
                });
        }

        // Language filter
        if ($this->language_filter) {
            $query->where(function (Builder $q): void {
                $q->whereHas('teachingLanguages', function (Builder $sub): void {
                    $sub->where('language', '=', $this->language_filter);
                })->orWhereHas('learningLanguages', function (Builder $sub): void {
                    $sub->where('language', '=', $this->language_filter);
                });
            });
        }

        return $query->latest()->paginate(12);
    }

    public function toggleTooltip(int $user_id): void
    {
        if (isset($this->open_tooltips[$user_id])) {
            unset($this->open_tooltips[$user_id]);
        } else {
            $this->open_tooltips = [];
            $this->open_tooltips[$user_id] = $user_id;
        }
    }

    public function closeTooltip(int $user_id): void
    {
        unset($this->open_tooltips[$user_id]);
    }

    public function inviteStudent(int $student_id): void
    {
        /** @var User $teacher */
        $teacher = Auth::user();

        if (! $teacher->isTeacher()) {
            session()->flash('error', __('users.only_teachers_can_invite'));

            return;
        }

        $student = User::find($student_id);

        if (! $student) {
            return;
        }

        // Check if already has pending invitation
        $pending_invitation = TeacherInvitation::query()
            ->where('teacher_id', '=', $teacher->id)
            ->where('student_id', '=', $student_id)
            ->where('status', '=', InvitationStatusEnum::PENDING->value)
            ->exists();

        if ($pending_invitation) {
            session()->flash('error', __('users.invitation_already_sent'));
            $this->closeTooltip($student_id);

            return;
        }

        // Check if already a student
        if ($teacher->students()->where('student_id', '=', $student_id)->exists()) {
            session()->flash('error', __('users.already_your_student'));
            $this->closeTooltip($student_id);

            return;
        }

        // Create invitation message
        $message = UserMessage::create([
            'sender_id' => $teacher->id,
            'recipient_id' => $student_id,
            'content' => __('users.invitation_message', ['teacher' => $teacher->full_name]),
            'message_type' => MessageTypeEnum::INVITATION->value,
        ]);

        // Create invitation record
        TeacherInvitation::create([
            'teacher_id' => $teacher->id,
            'student_id' => $student_id,
            'message_id' => $message->id,
            'status' => InvitationStatusEnum::PENDING->value,
        ]);

        session()->flash('message', __('users.invitation_sent'));
        $this->closeTooltip($student_id);
    }

    public function confirmLeaveTeacher(int $teacher_id): void
    {
        $this->user_to_remove = $teacher_id;
        $this->remove_action = 'leave_teacher';
        $this->show_confirm_modal = true;
        $this->closeTooltip($teacher_id);
    }

    public function confirmRemoveStudent(int $student_id): void
    {
        $this->user_to_remove = $student_id;
        $this->remove_action = 'remove_student';
        $this->show_confirm_modal = true;
        $this->closeTooltip($student_id);
    }

    public function executeRemove(): void
    {
        if ($this->user_to_remove === null) {
            $this->closeConfirmModal();

            return;
        }

        /** @var User $current_user */
        $current_user = Auth::user();

        if ($this->remove_action === 'leave_teacher') {
            $current_user->teachers()->detach($this->user_to_remove);
            session()->flash('message', __('users.left_teacher'));
        } elseif ($this->remove_action === 'remove_student' && $current_user->isTeacher()) {
            $current_user->students()->detach($this->user_to_remove);
            session()->flash('message', __('users.student_removed'));
        }

        $this->closeConfirmModal();
    }

    public function closeConfirmModal(): void
    {
        $this->show_confirm_modal = false;
        $this->user_to_remove = null;
        $this->remove_action = '';
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.user.user-list', [
            'users' => $this->getUsers(),
        ]);
    }
}
