<a href="{{ route('messages.index') }}"
   class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all {{ request()->is('messages*') ? 'bg-blue-50 text-blue-600' : '' }}">
    <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors relative">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        @if($unread_count > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold min-w-[14px] h-[14px] flex items-center justify-center rounded-full">
                {{ $unread_count > 9 ? '9+' : $unread_count }}
            </span>
        @endif
    </div>
    <span class="flex-1">{{ __('nav.messages') }}</span>
    @if($unread_count > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            {{ $unread_count > 99 ? '99+' : $unread_count }}
        </span>
    @endif
</a>
