<div class="relative" x-data="{ open: @entangle('is_open') }">
    <!-- Bell Button -->
    <button 
        type="button"
        wire:click="toggleDropdown"
        class="group relative px-2.5 py-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 focus:outline-none rounded-lg transition-all duration-200"
        :class="{ 'bg-blue-50 text-blue-600': open }"
        aria-label="{{ __('notifications.notifications') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-all duration-200" :class="{ 'animate-bounce': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Unread Badge -->
        @if($unread_count > 0)
            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-gradient-to-r from-red-500 to-pink-600 rounded-full shadow-lg animate-pulse">
                {{ $unread_count > 99 ? '99+' : $unread_count }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div 
        x-show="open"
        x-cloak
        @click.outside="$wire.closeDropdown()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-[0.625rem] w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
    >
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="text-lg font-bold text-gray-900">{{ __('notifications.title') }}</h3>
                @if($unread_count > 0)
                    <span class="px-2 py-0.5 text-xs font-semibold bg-blue-600 text-white rounded-full">{{ $unread_count }}</span>
                @endif
            </div>
            @if($unread_count > 0)
                <button 
                    type="button"
                    wire:click="markAllAsRead"
                    class="group text-xs text-blue-600 hover:text-blue-700 font-semibold px-3 py-1.5 hover:bg-blue-100 rounded-lg transition-all duration-200 flex items-center gap-1"
                >
                    <svg class="h-3.5 w-3.5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('notifications.mark_all_read') }}
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div 
                    wire:key="notification-{{ $notification->id }}"
                    data-notification-id="{{ $notification->id }}"
                    class="group px-5 py-4 border-b border-gray-100 hover:bg-blue-50 transition-all duration-200 {{ $notification->unread() ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500' : '' }}"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-0.5">
                            @php
                                $status = $notification->data['status'] ?? '';
                            @endphp
                            <div class="p-2 rounded-lg {{ $notification->unread() ? 'bg-white shadow-sm' : 'bg-gray-100' }}">
                                @if(str_contains($status, 'success') || str_contains($status, 'approved'))
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif(str_contains($status, 'rejected'))
                                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif(str_contains($status, 'processing'))
                                    <svg class="h-5 w-5 text-yellow-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                @elseif(str_contains($status, 'error'))
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ ucfirst($notification->data['entity'] ?? 'Notification') }}
                                </p>
                                @if($notification->unread())
                                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            
                            @if(!empty($notification->data['lesson_url']))
                                <a 
                                    href="{{ $notification->data['lesson_url'] }}" 
                                    class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium mt-2 hover:underline"
                                    wire:click="closeDropdown"
                                >
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('lessons.view_lesson') }}
                                </a>
                            @endif
                            
                            <p class="text-xs text-gray-400 mt-2">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($notification->unread())
                                <button
                                    type="button"
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
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
                                class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
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
                <div class="px-5 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">{{ __('notifications.no_notifications') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('notifications.all_caught_up') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    @if($notifications->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-400 cursor-not-allowed">
                            {{ __('notifications.previous') }}
                        </span>
                    @else
                        <button 
                            wire:click="previousPage"
                            class="group px-3 py-1.5 text-xs text-blue-600 hover:text-blue-700 font-medium hover:bg-blue-50 rounded-lg transition-all flex items-center gap-1"
                        >
                            <svg class="h-3 w-3 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('notifications.previous') }}
                        </button>
                    @endif

                    <span class="text-xs text-gray-500">
                        {{ __('notifications.page') }} {{ $notifications->currentPage() }}
                    </span>

                    @if($notifications->hasMorePages())
                        <button 
                            wire:click="nextPage"
                            class="group px-3 py-1.5 text-xs text-blue-600 hover:text-blue-700 font-medium hover:bg-blue-50 rounded-lg transition-all flex items-center gap-1"
                        >
                            {{ __('notifications.next') }}
                            <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-400 cursor-not-allowed">
                            {{ __('notifications.next') }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
