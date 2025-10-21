<x-app-layout>

    <div class="flex justify-end mb-2">
        <button
            class="text-sm text-gray-600 hover:text-blue-600"
            @click="tooltipLang = (tooltipLang === 'en' ? 'vi' : 'en')">
            🌐 Switch Explanation Language
        </button>
    </div>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-2">{{ $lesson->topic }}</h2>


        <div class="prose max-w-none">
            <p>{{ $lesson->lesson_text }}</p>
        </div>

        @if(!empty($lesson->grammar_data))
            <div class="mt-4">
                <h3 class="text-lg font-semibold">Grammar</h3>
                @foreach($lesson->grammar_data as $g)
                    <div class="mt-2 p-3 border rounded">
                        <div class="font-semibold">{{ $g['label'] ?? '' }}</div>
                        <div class="text-sm text-gray-700">{{ $g['explanation'] ?? '' }}</div>
                        @if(!empty($g['formula']))
                            <div class="mt-2 flex gap-2">
                                @foreach($g['formula'] as $part)
                                    <span class="px-2 py-1 bg-gray-100 rounded">{{ $part }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($g['example']))
                            <div class="mt-2 italic text-gray-600">Example: {{ $g['example'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if(!empty($lesson->vocabulary))
            <div class="mt-6">
                <h3 class="text-lg font-semibold">Vocabulary</h3>
                <ul class="mt-2">
                    @foreach($lesson->vocabulary as $v)
                        <li class="mb-1">
                            <strong>{{ $v['word'] }}</strong> — <span class="text-gray-600">{{ $v['translation'] ?? '' }}</span>
                            <div class="text-sm italic text-gray-500">{{ $v['example'] ?? '' }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($lesson->quiz))
            <div class="mt-6">
                <livewire:vocabulary-quiz :quiz="$lesson->quiz" />
            </div>
        @endif
    </div>
</x-app-layout>
