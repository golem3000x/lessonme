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
        dd('asasa');
        $notification = Auth::user()
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
        dd('aaaa');
        Auth::user()->unreadNotifications->markAsRead();
        $this->updateUnreadCount();
    }

    public function deleteNotification(string $notification_id): void
    {
        dd('oooooooo');
        Auth::user()
            ->notifications()
            ->where('id', $notification_id)
            ->delete();

        $this->updateUnreadCount();
    }

    protected function updateUnreadCount(): void
    {
        $this->unread_count = Auth::user()->unreadNotifications()->count();
    }

    public function render(): Factory|View
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
