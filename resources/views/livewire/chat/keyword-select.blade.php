<div>
    @if(!empty($keywords))
        <label class="sr-only" for="keyword-select">{{ __('chat.keyword_label') }}</label>
        <select id="keyword-select" multiple wire:model.live="keyword_ids" class="border rounded px-2 py-1 min-w-48" size="4">
            @foreach($keywords as $k)
                <option value="{{ $k['id'] }}">{{ $k['label'] }}</option>
            @endforeach
        </select>
    @endif
</div>
