<span>
    @if($unread_count > 0)
        <span class="bg-red-500 text-white text-xs font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full px-1">
            {{ $unread_count > 99 ? '99+' : $unread_count }}
        </span>
    @endif
</span>
