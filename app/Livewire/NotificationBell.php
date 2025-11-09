<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unread_count = 0;

    public function mount(): void
    {
        $this->updateUnreadCount();
    }

    public function markAsRead(string $notification_id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $notification = $user
            ->notifications()
            ->where('id', $notification_id)
            ->first();

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            $this->updateUnreadCount();
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->updateUnreadCount();
    }

    public function deleteNotification(string $notification_id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user
            ->notifications()
            ->where('id', $notification_id)
            ->delete();

        $this->updateUnreadCount();
    }

    protected function updateUnreadCount(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->unread_count = 0;

            return;
        }

        $this->unread_count = $user->unreadNotifications()->count();
    }

    public function render(): Factory|View
    {
        $user = Auth::user();
        if (! $user) {
            return view('livewire.notification-bell', [
                'notifications' => collect(),
            ]);
        }

        $notifications = $user
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
