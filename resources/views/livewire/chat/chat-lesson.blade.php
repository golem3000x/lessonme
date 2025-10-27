<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">
            {{ __('chat.title') }} — {{ $topic }}
        </h3>

        <div class="flex items-center space-x-2">
            <label class="sr-only" for="topic-select">{{ __('chat.topic_label') }}</label>
            <select id="topic-select" wire:model.live="topic_id" class="border rounded px-2 py-1">
                @foreach($topics as $t)
                    <option value="{{ $t['id'] }}">{{ $t['label'] }}</option>
                @endforeach
            </select>

            <livewire:chat.keyword-select :topic_id="$topic_id" :keyword_ids="$keyword_ids" />

            <button
                    wire:click="startLesson"
                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition"
            >
                {{ __('chat.start') }}
            </button>
        </div>
    </div>

    <div class="h-72 overflow-y-auto border p-4 mb-4 bg-gray-50 rounded">
        @forelse($messages as $m)
            <div class="mb-3 {{ $m['sender'] === 'user' ? 'text-right' : 'text-left' }}">
                <span
                        class="inline-block px-3 py-2 rounded-xl {{ $m['sender'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}"
                >
                    {{ $m['content'] }}
                </span>
            </div>
        @empty
            <div class="text-center text-gray-400 italic">
                {{ __('chat.no_messages') }}
            </div>
        @endforelse
    </div>

    <div class="flex">
        <input
                wire:model="input"
                wire:keydown.enter="send"
                class="flex-grow border rounded px-3 py-2"
                placeholder="{{ __('chat.placeholder') }}"
        />
        <button
                wire:click="send"
                class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
            {{ __('chat.send') }}
        </button>
    </div>
</div>