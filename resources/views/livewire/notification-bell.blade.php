<div class="relative">
    <!-- Bell Button -->
    <button 
        type="button"
        wire:click="toggleDropdown"
        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-lg transition"
        aria-label="{{ __('notifications.notifications') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Unread Badge -->
        @if($unread_count > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unread_count > 99 ? '99+' : $unread_count }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    @if($is_open)
    <div 
        x-data 
        @click.outside="$wire.closeDropdown()"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('notifications.title') }}</h3>
            @if($unread_count > 0)
                <button 
                    type="button"
                    wire:click="markAllAsRead"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >
                    {{ __('notifications.mark_all_read') }}
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            @forelse($notifications as $notification)
                <div 
                    wire:key="notification-{{ $notification->id }}"
                    data-notification-id="{{ $notification->id }}"
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition {{ $notification->unread() ? 'bg-blue-50' : '' }}"
                >
                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            @php
                                $status = $notification->data['status'] ?? '';
                            @endphp
                            @if(str_contains($status, 'success') || str_contains($status, 'approved'))
                                <span class="text-green-500 text-xl">✅</span>
                            @elseif(str_contains($status, 'rejected'))
                                <span class="text-red-500 text-xl">❌</span>
                            @elseif(str_contains($status, 'processing'))
                                <span class="text-yellow-500 text-xl">⏳</span>
                            @elseif(str_contains($status, 'error'))
                                <span class="text-red-600 text-xl">⚠️</span>
                            @else
                                <span class="text-blue-500 text-xl">🔔</span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ ucfirst($notification->data['entity'] ?? 'Notification') }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            
                            @if(!empty($notification->data['lesson_url']))
                                <a 
                                    href="{{ $notification->data['lesson_url'] }}" 
                                    class="text-xs text-indigo-600 hover:text-indigo-800 underline mt-1 inline-block"
                                    wire:click="closeDropdown"
                                >
                                    {{ __('lessons.view_lesson') }}
                                </a>
                            @endif
                            
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex space-x-1">
                            @if($notification->unread())
                                <button
                                    type="button"
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="text-indigo-600 hover:text-indigo-800 p-1"
                                    title="{{ __('notifications.mark_read') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endif
                            <button
                                type="button"
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                class="text-red-600 hover:text-red-800 p-1"
                                title="{{ __('notifications.delete') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-sm">{{ __('notifications.no_notifications') }}</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
