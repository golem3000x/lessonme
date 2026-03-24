<div class="flex h-full bg-gray-100 overflow-hidden">
    {{-- Conversations List - full width on mobile when no conversation selected, hidden when conversation is selected on mobile --}}
    <div class="w-full md:w-80 bg-white border-r border-gray-200 flex flex-col {{ $selected_user ? 'hidden md:flex' : 'flex' }}">
        {{-- Header --}}
        <div class="h-[72px] px-4 border-b border-gray-200 flex items-center">
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

    {{-- Chat Area - hidden on mobile when no conversation selected --}}
    <div class="flex-1 flex flex-col min-w-0 {{ $selected_user ? 'flex' : 'hidden md:flex' }}">
        @if($selected_user)
            {{-- Chat Header --}}
            <div class="bg-white border-b border-gray-200 h-[72px] px-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- Back button - only on mobile --}}
                    <button wire:click="deselectConversation"
                            class="md:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    @if($selected_user->avatar)
                        <img src="{{ $selected_user->avatar_url }}" 
                             alt="{{ $selected_user->name }}"
                             class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr($selected_user->name, 0, 2)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $selected_user->name }}</h3>
                        <a href="{{ route('profile.show', $selected_user) }}" class="text-sm text-blue-600 hover:underline">
                            {{ __('messages.view_profile') }}
                        </a>
                    </div>
                </div>
                
                {{-- Actions dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="group p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200"
                            :class="{ 'bg-blue-50 text-blue-600': open }"
                            title="{{ __('messages.actions') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>

                    {{-- Dropdown menu --}}
                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden"
                         x-cloak>
                        
                        {{-- Report --}}
                        <button wire:click="showReportModal" @click="open = false"
                                class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-orange-600 hover:bg-orange-50 transition-all duration-200">
                            <div class="p-1.5 rounded-lg bg-orange-50 group-hover:bg-orange-100 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <span class="font-medium">{{ __('messages.report_user') }}</span>
                        </button>

                        <div class="border-t border-gray-100"></div>

                        {{-- Block --}}
                        <button wire:click="showBlockModal" @click="open = false"
                                class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-200">
                            <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <span class="font-medium">{{ __('messages.block_user') }}</span>
                        </button>

                        <div class="border-t border-gray-100"></div>

                        {{-- Delete --}}
                        <button wire:click="showDeleteModal" @click="open = false"
                                class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-200">
                            <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <span class="font-medium">{{ __('messages.delete_conversation') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-4" 
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
                                <p class="text-sm break-words">{{ $message['content'] }}</p>
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
            <div class="bg-white border-t border-gray-200 px-4 sm:px-6 py-3 sm:py-4 min-w-0 flex-shrink-0">
                <form wire:submit="sendMessage" class="flex items-center gap-2 sm:gap-3 w-full">
                    <input type="text"
                           wire:model="new_message"
                           placeholder="{{ __('messages.type_message') }}"
                           class="flex-1 min-w-0 border border-gray-300 rounded-full px-4 py-2 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="off">
                    <button type="submit"
                            class="flex-shrink-0 bg-blue-600 text-white p-2.5 sm:p-3 rounded-full hover:bg-blue-700 transition">
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

    {{-- Delete Conversation Modal --}}
    @if($show_delete_modal)
        <x-confirm-modal-livewire
            icon="delete"
            :title="__('messages.delete_conversation_title')"
            :message="__('messages.delete_conversation_message')"
            :confirmText="__('messages.delete')"
            :cancelText="__('messages.cancel')"
            confirmMethod="deleteConversation"
            cancelMethod="closeDeleteModal"
        />
    @endif

    {{-- Block User Modal --}}
    @if($show_block_modal)
        <x-confirm-modal-livewire
            icon="danger"
            :title="__('messages.block_user_title')"
            :message="__('messages.block_user_message')"
            :confirmText="__('messages.block')"
            :cancelText="__('messages.cancel')"
            confirmMethod="blockUser"
            cancelMethod="closeBlockModal"
        />
    @endif

    {{-- Report User Modal --}}
    @if($show_report_modal)
        <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$wire.closeReportModal()">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100" @click.stop>
                    {{-- Icon --}}
                    <div class="flex justify-center pt-8 pb-4">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-full">
                            <svg class="h-10 w-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="px-6 pb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 text-center">
                            {{ __('messages.report_user_title') }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-6 text-center">
                            {{ __('messages.report_user_message') }}
                        </p>

                        {{-- Report Form --}}
                        <div class="space-y-4">
                            {{-- Reason Select --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('messages.report_reason') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="report_reason"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">{{ __('messages.select_reason') }}</option>
                                    @foreach($report_reasons as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('report_reason')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('messages.report_description') }}
                                </label>
                                <textarea wire:model="report_description"
                                          rows="3"
                                          maxlength="1000"
                                          placeholder="{{ __('messages.report_description_placeholder') }}"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"></textarea>
                                <p class="text-xs text-gray-500 mt-1">{{ strlen($report_description) }}/1000</p>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-3 mt-6">
                            <button wire:click="closeReportModal"
                                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                                {{ __('messages.cancel') }}
                            </button>
                            <button wire:click="submitReport"
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-600 to-yellow-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-yellow-700 shadow-md hover:shadow-lg transition-all duration-200">
                                {{ __('messages.submit_report') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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

    // iOS keyboard fix - set CSS variable for viewport height
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            document.documentElement.style.setProperty('--viewport-height', `${window.visualViewport.height}px`);
        });
        document.documentElement.style.setProperty('--viewport-height', `${window.visualViewport.height}px`);
    }
</script>
@endscript
