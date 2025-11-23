<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class ChatButton extends Component
{
    public bool $is_chat_open = false;

    public int $unread_count = 0;

    public function toggleChat(): void
    {
        $this->is_chat_open = ! $this->is_chat_open;

        // Диспетчим событие для открытия/закрытия панели чата
        $this->dispatch('chat-toggled', is_open: $this->is_chat_open);

        // Если открыли чат, сбрасываем счетчик непрочитанных
        if ($this->is_chat_open) {
            $this->unread_count = 0;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.chat-button');
    }
}
