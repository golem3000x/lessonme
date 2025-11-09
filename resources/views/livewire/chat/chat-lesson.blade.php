<div>
    <h2>{{ $topic }}</h2>
    
    <div class="mb-4">
        <label for="topic-select" class="block text-sm font-medium text-gray-700">Select Topic</label>
        <select id="topic-select" wire:model.live="topic_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">-- Select a Topic --</option>
            @foreach($topics as $t)
                <option value="{{ $t['id'] }}">{{ $t['label'] }}</option>
            @endforeach
        </select>
    </div>

    @if(count($keywords) > 0)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Keywords</label>
            <div class="mt-2 space-y-2">
                @foreach($keywords as $kw)
                    <div>
                        <input
                            type="checkbox"
                            id="keyword-{{ $kw['id'] }}"
                            value="{{ $kw['id'] }}"
                            wire:model="keyword_ids"
                        />
                        <label for="keyword-{{ $kw['id'] }}">{{ $kw['label'] }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
