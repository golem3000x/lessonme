<div>
    <div x-data="{ open: false }" class="relative">
        <button 
            type="button" 
            @click="open = !open" 
            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white flex items-center justify-between hover:bg-gray-50 transition focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
            <span class="text-sm text-gray-700">{{ __('lessons.keywords_label') }}</span>
            <div class="flex items-center gap-2">
                @if(count($keyword_ids) > 0)
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ count($keyword_ids) }}
                    </span>
                @else
                    <span class="text-gray-400 text-xs">0</span>
                @endif
                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
            </div>
        </button>

        <div 
            x-cloak 
            x-show="open" 
            @click.outside="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute left-0 right-0 z-50 mt-1 max-h-60 overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg"
        >
            @if(count($keywords) > 0)
                <div class="p-2 space-y-1">
                    @foreach($keywords as $k)
                        <label for="kw-{{ $k['id'] }}" class="flex items-center space-x-2 p-2 rounded cursor-pointer hover:bg-blue-50 transition">
                            <input 
                                id="kw-{{ $k['id'] }}" 
                                type="checkbox" 
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                @if(in_array($k['id'], $keyword_ids)) checked @endif
                                wire:click="toggleKeyword({{ $k['id'] }})"
                            >
                            <span class="text-sm text-gray-800">{{ $k['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="p-4 text-center text-gray-500 text-sm">
                    {{ __('lessons.no_keywords') }}
                </div>
            @endif
        </div>
    </div>
</div>
