<?php

declare(strict_types=1);

namespace App\Livewire\Messages;

use App\Models\UserMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UnreadMessagesBadge extends Component
{
    public int $unread_count = 0;

    public function mount(): void
    {
        $this->loadUnreadCount();
    }

    public function loadUnreadCount(): void
    {
        $user_id = Auth::id();

        if (! $user_id) {
            $this->unread_count = 0;

            return;
        }

        $this->unread_count = UserMessage::query()
            ->where('recipient_id', '=', $user_id)
            ->whereNull('read_at')
            ->count();
    }

    #[On('dm-received')]
    public function onMessageReceived(
        int $id,
        int $sender_id,
        int $recipient_id,
        string $content,
        ?string $read_at,
        ?string $created_at,
        ?string $sender_name,
        ?string $sender_avatar
    ): void {
        $this->loadUnreadCount();
    }

    #[On('messages-read')]
    public function onMessagesRead(): void
    {
        $this->loadUnreadCount();
    }

    public function render(): View
    {
        return view('livewire.messages.unread-messages-badge');
    }
}
