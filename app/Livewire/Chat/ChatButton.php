<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use Livewire\Attributes\On;
use Livewire\Component;

class ChatButton extends Component
{
    public bool $is_chat_open = false;

    public int $unread_count = 0;

    #[On('chat-toggled')]
    public function syncChatState(bool $is_open): void
    {
        $this->is_chat_open = $is_open;

        if ($is_open) {
            $this->unread_count = 0;
        }
    }

    public function toggleChat(): void
    {
        $this->is_chat_open = ! $this->is_chat_open;

        // Dispatch event to open/close chat panel
        $this->dispatch('chat-toggled', is_open: $this->is_chat_open);

        // If chat opened, reset unread count
        if ($this->is_chat_open) {
            $this->unread_count = 0;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.chat.chat-button');
    }
}
