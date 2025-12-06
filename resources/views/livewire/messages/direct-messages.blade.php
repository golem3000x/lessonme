<div class="flex h-full bg-gray-100">
    {{-- Conversations List --}}
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ __('messages.title') }}
                @if($unread_count > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unread_count }}</span>
                @endif
            </h2>
        </div>

        {{-- Conversations --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conversation)
                <button wire:click="selectConversation({{ $conversation['user_id'] }})"
                        class="w-full p-4 text-left hover:bg-gray-50 transition border-b border-gray-100 {{ $conversation['is_selected'] ? 'bg-blue-50' : '' }}">
                    <div class="flex items-center gap-3">
                        @if($conversation['user_avatar'])
                            <img src="{{ $conversation['user_avatar'] }}" 
                                 alt="{{ $conversation['user_name'] }}"
                                 class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-lg font-bold text-white">
                                    {{ strtoupper(substr($conversation['user_name'] ?? '', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900 truncate">{{ $conversation['user_name'] }}</span>
                                @if($conversation['unread_count'] > 0)
                                    <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                                        {{ $conversation['unread_count'] }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 truncate">{{ $conversation['last_message'] }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($conversation['last_message_at'])->diffForHumans() }}</p>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p>{{ __('messages.no_conversations') }}</p>
                    <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                        {{ __('messages.find_users') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Area --}}
    <div class="flex-1 flex flex-col">
        @if($selected_user)
            {{-- Chat Header --}}
            <div class="bg-white border-b border-gray-200 p-4 flex items-center gap-3">
                @if($selected_user->avatar)
                    <img src="{{ $selected_user->avatar_url }}" 
                         alt="{{ $selected_user->full_name }}"
                         class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-sm font-bold text-white">
                            {{ strtoupper(substr($selected_user->first_name, 0, 1) . substr($selected_user->last_name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $selected_user->full_name }}</h3>
                    <a href="{{ route('profile.show', $selected_user) }}" class="text-sm text-blue-600 hover:underline">
                        {{ __('messages.view_profile') }}
                    </a>
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4" 
                 id="messages-container"
                 x-data="{ 
                     scrollToBottom() { 
                         this.$el.scrollTop = this.$el.scrollHeight; 
                     } 
                 }"
                 x-init="scrollToBottom()"
                 x-on:dm-sent.window="$nextTick(() => scrollToBottom())"
                 x-on:dm-received.window="$nextTick(() => scrollToBottom())"
                 x-on:message-scroll.window="$nextTick(() => scrollToBottom())"
                 wire:ignore.self>
                @foreach($messages as $message)
                    @php
                        $is_mine = $message['sender_id'] === auth()->id();
                        $is_invitation = ($message['message_type'] ?? 'text') === 'invitation';
                        $invitation = $message['invitation'] ?? null;
                    @endphp
                    <div class="flex {{ $is_mine ? 'justify-end' : 'justify-start' }}">
                        @if($is_invitation)
                            {{-- Invitation message --}}
                            <div class="max-w-[80%] bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-2xl px-4 py-3 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-1.5 bg-green-100 rounded-full">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-green-800">{{ __('messages.invitation') }}</span>
                                </div>
                                <p class="text-sm text-gray-700 mb-3">{{ $message['content'] }}</p>
                                
                                @if($invitation)
                                    @if($invitation['status'] === 'pending' && !$is_mine)
                                        {{-- Show Accept/Decline buttons for recipient --}}
                                        <div class="flex gap-2 mt-2">
                                            <button wire:click="acceptInvitation({{ $invitation['id'] }})"
                                                    class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ __('messages.accept') }}
                                            </button>
                                            <button wire:click="declineInvitation({{ $invitation['id'] }})"
                                                    class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                {{ __('messages.decline') }}
                                            </button>
                                        </div>
                                    @elseif($invitation['status'] === 'accepted')
                                        <div class="flex items-center gap-2 mt-2 text-green-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm font-medium">{{ __('messages.invitation_accepted_status') }}</span>
                                        </div>
                                    @elseif($invitation['status'] === 'declined')
                                        <div class="flex items-center gap-2 mt-2 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span class="text-sm font-medium">{{ __('messages.invitation_declined_status') }}</span>
                                        </div>
                                    @elseif($invitation['status'] === 'pending' && $is_mine)
                                        <div class="flex items-center gap-2 mt-2 text-yellow-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-medium">{{ __('messages.invitation_pending_status') }}</span>
                                        </div>
                                    @endif
                                @endif
                                
                                <p class="text-xs text-gray-400 mt-2">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                                </p>
                            </div>
                        @else
                            {{-- Regular message --}}
                            <div class="max-w-[70%] {{ $is_mine ? 'bg-blue-600 text-white' : 'bg-white text-gray-900' }} rounded-2xl px-4 py-2 shadow-sm">
                                <p class="text-sm">{{ $message['content'] }}</p>
                                <p class="text-xs {{ $is_mine ? 'text-blue-200' : 'text-gray-400' }} mt-1">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                                    @if($is_mine && !empty($message['read_at']))
                                        <span class="ml-1">✓✓</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Input --}}
            <div class="bg-white border-t border-gray-200 p-4">
                <form wire:submit="sendMessage" class="flex items-center gap-3">
                    <input type="text"
                           wire:model="new_message"
                           placeholder="{{ __('messages.type_message') }}"
                           class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="off">
                    <button type="submit"
                            class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            {{-- No conversation selected --}}
            <div class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.select_conversation') }}</h3>
                    <p class="text-gray-500">{{ __('messages.select_conversation_hint') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    // Auto-scroll after Livewire updates
    Livewire.hook('morph.updated', ({ el }) => {
        if (el.id === 'messages-container') {
            el.scrollTop = el.scrollHeight;
        }
    });
    
    // Also scroll when component is initialized
    $wire.on('messages-loaded', () => {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endscript
