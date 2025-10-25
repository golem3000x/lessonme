<x-app-layout>

    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-2">{{ $lesson->lesson_name }}</h2>

        <div class="prose max-w-none">
            <p>{{ $lesson->lesson_text }}</p>
        </div>

        @php
            $grammar = is_array($lesson->grammar_data) ? $lesson->grammar_data : [];
        @endphp
        @if(!empty($grammar))
            <div class="mt-4">
                <h3 class="text-lg font-semibold">{{ __('lessons.grammar') }}</h3>
                @foreach($grammar as $g)
                    @php
                        $formula = $g['formula'] ?? null;
                        $formulaParts = is_array($formula) ? $formula : (isset($formula) ? [$formula] : []);
                    @endphp
                    <div class="mt-2 p-3 border rounded">
                        <div class="font-semibold">{{ $g['label'] ?? '' }}</div>
                        <div class="text-sm text-gray-700">{{ $g['explanation'] ?? '' }}</div>
                        @if(!empty($formulaParts))
                            <div class="mt-2 flex gap-2 flex-wrap">
                                @foreach($formulaParts as $part)
                                    <span class="px-2 py-1 bg-gray-100 rounded">{{ $part }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($g['example']))
                            <div class="mt-2 italic text-gray-600">{{ __('lessons.example') }}
                                : {{ $g['example'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @php
            $vocab = is_array($lesson->vocabulary) ? $lesson->vocabulary : [];
        @endphp
        @if(!empty($vocab))
            <div class="mt-6">
                <h3 class="text-lg font-semibold">{{ __('lessons.vocabulary') }}</h3>
                <ul class="mt-2">
                    @foreach($vocab as $v)
                        <li class="mb-1">
                            <strong>{{ $v['word'] ?? '' }}</strong> — <span
                                    class="text-gray-600">{{ $v['translation'] ?? '' }}</span>
                            @if(!empty($v['example']))
                                <div class="text-sm italic text-gray-500">{{ $v['example'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $quiz = is_array($lesson->quiz) ? $lesson->quiz : [];
        @endphp
        @if(!empty($quiz))
            <div class="mt-6">
                <livewire:vocabulary-quiz :quiz="$quiz"/>
            </div>
        @endif
    </div>
</x-app-layout>
