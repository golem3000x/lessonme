<!-- Chat panel -->
<div 
    x-data="{ 
        scrollToBottom() { 
            this.$nextTick(() => {
                setTimeout(() => {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 50);
            });
        }
    }"
    x-init="$watch('$wire.is_open', value => { if (value) scrollToBottom() })"
    @message-sent.window="scrollToBottom()"
    @message-received.window="scrollToBottom()"
    :class="$wire.is_open ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 pointer-events-none'"
    class="fixed inset-x-2 bottom-20 sm:inset-x-auto sm:bottom-24 sm:right-6 sm:w-96 h-[70vh] sm:h-[500px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden z-40 transition-all duration-300"
    x-cloak
>
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white font-semibold text-lg">{{ __('nav.chat_support') }}</h3>
                <p class="text-blue-100 text-xs">{{ __('nav.chat_online') }}</p>
            </div>
        </div>
        <button 
            wire:click="closeChat"
            class="text-white hover:bg-white/20 rounded-lg p-2 transition"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Messages -->
    <div 
        x-ref="messagesContainer"
        class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-4 bg-gray-50"
        style="scroll-behavior: smooth;"
    >
        @forelse($messages as $message)
            <div class="flex {{ $message['is_from_admin'] ? 'justify-start' : 'justify-end' }} animate-fade-in">
                <div class="flex items-start gap-2 max-w-[80%] min-w-0 {{ $message['is_from_admin'] ? 'flex-row' : 'flex-row-reverse' }}">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($message['is_from_admin'])
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @else
                            @if(!empty($message['user']['avatar_url']))
                                <img 
                                    src="{{ $message['user']['avatar_url'] }}" 
                                    alt="{{ $message['user']['name'] }}"
                                    class="w-8 h-8 rounded-full ring-2 ring-blue-100 object-cover"
                                >
                            @else
                                <img 
                                    src="https://ui-avatars.com/api/?name={{ urlencode($message['user']['name']) }}&background=2563EB&color=fff" 
                                    alt="{{ $message['user']['name'] }}"
                                    class="w-8 h-8 rounded-full ring-2 ring-blue-100"
                                >
                            @endif
                        @endif
                    </div>

                    <!-- Message -->
                    <div class="min-w-0">
                        <div class="
                            {{ $message['is_from_admin'] 
                                ? 'bg-white border border-gray-200' 
                                : 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' 
                            }}
                            rounded-2xl px-4 py-2 shadow-sm
                            {{ $message['is_from_admin'] ? 'rounded-tl-none' : 'rounded-tr-none' }}
                        ">
                            <p class="text-sm break-words">{{ $message['message'] }}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 {{ $message['is_from_admin'] ? 'text-left' : 'text-right' }}">
                            {{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm">{{ __('nav.chat_no_messages') }}</p>
                <p class="text-xs mt-1">{{ __('nav.chat_start_conversation') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Input form -->
    <div class="p-4 bg-white border-t border-gray-200">
        <form wire:submit="sendMessage" class="flex gap-2">
            <input 
                type="text" 
                wire:model="new_message"
                placeholder="{{ __('nav.chat_enter_message') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                maxlength="1000"
            >
            <button 
                type="submit"
                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full p-2 hover:shadow-lg transition-all hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
            >
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-6 w-6" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                    wire:loading.class="animate-spin"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                    />
                </svg>
            </button>
        </form>
        @error('new_message')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.2s ease-out;
        }
    </style>
</div>
