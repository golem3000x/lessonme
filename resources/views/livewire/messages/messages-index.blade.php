<div class="flex flex-col h-full bg-gray-100">
    {{-- Safety Warning Banner --}}
    @if($show_safety_warning)
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-4 text-white relative">
            <button 
                wire:click="hideSafetyWarning"
                class="absolute top-3 right-3 p-1 text-white/70 hover:text-white transition-all duration-300 hover:rotate-90 hover:scale-110"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex items-start gap-3 pr-8">
                <span class="text-2xl">🛡️</span>
                <div>
                    <h3 class="font-bold text-lg">{{ __('messages.safety_title') }}</h3>
                    <p class="text-amber-100 text-sm">{{ __('messages.safety_description') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-1 min-h-0">
    {{-- Conversations List --}}
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ __('messages.title') }}
            </h2>
        </div>

        {{-- Conversations --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conversation)
                <a href="{{ route('messages.chat', $conversation['user']) }}"
                   class="w-full p-4 text-left hover:bg-gray-50 transition border-b border-gray-100 flex items-center gap-3">
                    @if($conversation['user']?->avatar)
                        <img src="{{ $conversation['user']->avatar_url }}" 
                             alt="{{ $conversation['user']->full_name }}"
                             class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <span class="text-lg font-bold text-white">
                                {{ strtoupper(substr($conversation['user']?->first_name ?? '', 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 truncate">{{ $conversation['user']?->full_name ?? __('messages.deleted_user') }}</span>
                            @if($conversation['unread_count'] > 0)
                                <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                                    {{ $conversation['unread_count'] }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 truncate">{{ $conversation['last_message'] }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($conversation['last_message_at'])->diffForHumans() }}</p>
                    </div>
                </a>
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

    {{-- Empty Chat Area --}}
    <div class="flex-1 flex items-center justify-center bg-gray-50">
        <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.select_conversation') }}</h3>
            <p class="text-gray-500">{{ __('messages.select_conversation_hint') }}</p>
        </div>
    </div>
    </div>
</div>
