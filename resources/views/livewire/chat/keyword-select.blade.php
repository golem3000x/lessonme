<div>
    @if(!empty($keywords))
        <div x-data="{ open: false }" class="relative">
            <button type="button" @click="open = !open" class="border rounded px-2 py-1 min-w-48 bg-white flex items-center justify-between">
                <span>{{ __('chat.keyword_label') }}</span>
                @if(count($keyword_ids) > 0)
                    <span class="ml-2 text-gray-500 text-sm">({{ count($keyword_ids) }})</span>
                @endif
                <svg class="ml-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
            </button>

            <div x-cloak x-show="open" @click.outside="open = false" class="absolute right-0 z-50 mt-1 w-64 max-h-60 overflow-y-auto rounded border bg-white p-2 shadow">
                <div class="space-y-1">
                    @foreach($keywords as $k)
                        <label for="kw-{{ $k['id'] }}" class="flex items-center space-x-2 py-1 cursor-pointer">
                            <input id="kw-{{ $k['id'] }}" type="checkbox" class="rounded" wire:model.live="keyword_ids" value="{{ $k['id'] }}">
                            <span class="text-sm text-gray-800">{{ $k['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
