<?php

declare(strict_types=1);

namespace App\Livewire\Messages;

use App\Events\UserMessageSent;
use App\Models\TeacherInvitation;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DirectMessages extends Component
{
    public ?User $selected_user = null;

    public string $new_message = '';

    /** @var array<int, array<string, mixed>> */
    public array $messages = [];

    /** @var array<int, array<string, mixed>> */
    public array $conversations = [];

    public int $unread_count = 0;

    public function mount(?User $user = null): void
    {
        if ($user && $user->id !== Auth::id()) {
            $this->selected_user = $user;
            $this->loadMessages();
        }

        $this->loadConversations();
        $this->loadUnreadCount();
    }

    public function loadConversations(): void
    {
        /** @var User $current_user */
        $current_user = Auth::user();

        // Get all users we have conversations with
        $conversations = UserMessage::query()
            ->where('sender_id', '=', $current_user->id)
            ->orWhere('recipient_id', '=', $current_user->id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function (UserMessage $message) use ($current_user): int {
                return $message->sender_id === $current_user->id
                    ? $message->recipient_id
                    : $message->sender_id;
            })
            ->map(function (Collection $messages, int $user_id) use ($current_user): array {
                $last_message = $messages->first();
                $other_user = User::find($user_id);
                $unread = $messages->where('recipient_id', '=', $current_user->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'user_id' => $user_id,
                    'user_name' => $other_user?->full_name,
                    'user_avatar' => $other_user?->avatar_url,
                    'last_message' => $last_message->content,
                    'last_message_at' => $last_message->created_at,
                    'unread_count' => $unread,
                    'is_selected' => $this->selected_user?->id === $user_id,
                ];
            })
            ->values()
            ->toArray();

        $this->conversations = $conversations;
    }

    public function loadMessages(): void
    {
        if (! $this->selected_user) {
            $this->messages = [];

            return;
        }

        /** @var User $current_user */
        $current_user = Auth::user();

        $this->messages = UserMessage::query()
            ->where(function (Builder $query) use ($current_user): void {
                $query->where('sender_id', '=', $current_user->id)
                    ->where('recipient_id', '=', $this->selected_user->id);
            })
            ->orWhere(function (Builder $query) use ($current_user): void {
                $query->where('sender_id', '=', $this->selected_user->id)
                    ->where('recipient_id', '=', $current_user->id);
            })
            ->with(['sender', 'recipient', 'invitation'])
            ->orderBy('created_at')
            ->get()
            ->toArray();

        // Mark messages as read
        $this->markMessagesAsRead();
    }

    public function loadUnreadCount(): void
    {
        /** @var User $current_user */
        $current_user = Auth::user();

        $this->unread_count = UserMessage::query()
            ->where('recipient_id', '=', $current_user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function selectConversation(int $user_id): void
    {
        $this->selected_user = User::find($user_id);
        $this->loadMessages();
        $this->loadConversations();
        $this->loadUnreadCount();
    }

    public function sendMessage(): void
    {
        if (! $this->selected_user) {
            return;
        }

        $this->validate([
            'new_message' => 'required|string|max:1000',
        ]);

        /** @var User $current_user */
        $current_user = Auth::user();

        $message = UserMessage::create([
            'sender_id' => $current_user->id,
            'recipient_id' => $this->selected_user->id,
            'content' => $this->new_message,
        ]);

        // Load relationships for event
        $message->load(['sender', 'recipient']);

        // Broadcast to recipient
        broadcast(new UserMessageSent($message))->toOthers();

        // Add message to local array
        $this->messages[] = $message->toArray();

        // Clear input
        $this->new_message = '';

        // Refresh conversations
        $this->loadConversations();

        // Dispatch event to scroll to bottom
        $this->dispatch('dm-sent');
    }

    protected function markMessagesAsRead(): void
    {
        if (! $this->selected_user) {
            return;
        }

        /** @var User $current_user */
        $current_user = Auth::user();

        $updated = UserMessage::query()
            ->where('sender_id', '=', $this->selected_user->id)
            ->where('recipient_id', '=', $current_user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Notify counter to update if messages were marked as read
        if ($updated > 0) {
            $this->dispatch('messages-read');
        }
    }

    #[On('dm-received')]
    public function receiveMessage(
        int $id,
        int $sender_id,
        int $recipient_id,
        string $content,
        ?string $read_at,
        ?string $created_at,
        ?string $sender_name,
        ?string $sender_avatar
    ): void {
        // Check if message is for current conversation
        if ($this->selected_user && $sender_id === $this->selected_user->id) {
            // Load full message with relationships
            /** @var UserMessage|null $message */
            $message = UserMessage::with(['sender', 'recipient'])->find($id);

            if ($message) {
                // Check if message already exists
                $message_ids = array_column($this->messages, 'id');

                if (! in_array($message->id, $message_ids)) {
                    $this->messages[] = $message->toArray();

                    // Mark as read immediately since chat is open
                    $message->markAsRead();

                    $this->dispatch('message-scroll');
                }
            }
        }

        // Always refresh conversations and unread count
        $this->loadConversations();
        $this->loadUnreadCount();
    }

    public function acceptInvitation(int $invitation_id): void
    {
        $invitation = TeacherInvitation::find($invitation_id);

        if (! $invitation || $invitation->student_id !== Auth::id()) {
            return;
        }

        if (! $invitation->isPending()) {
            return;
        }

        $invitation->accept();

        // Reload messages to update the invitation status
        $this->loadMessages();

        session()->flash('message', __('messages.invitation_accepted'));
    }

    public function declineInvitation(int $invitation_id): void
    {
        $invitation = TeacherInvitation::find($invitation_id);

        if (! $invitation || $invitation->student_id !== Auth::id()) {
            return;
        }

        if (! $invitation->isPending()) {
            return;
        }

        $invitation->decline();

        // Reload messages to update the invitation status
        $this->loadMessages();

        session()->flash('message', __('messages.invitation_declined'));
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.messages.direct-messages');
    }
}
