<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-10">

        {{-- ===== HEADER / INTRO ===== --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-3xl font-bold text-indigo-700 mb-3">
                📘 {{ $lesson->lesson_name }}
            </h2>
            <div class="flex items-center gap-3 text-sm text-gray-500 mb-6">
                @if($lesson->language_level)
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $lesson->level_badge_color }}">
                        {{ $lesson->language_level->value }}
                    </span>
                @endif
                <span>{{ __('lessons.topic') }}: {{ $lesson->topic?->title() ?? '—' }}</span>
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $lesson->formatted_created_at }}
                </span>
            </div>

            <div class="prose prose-indigo max-w-none leading-relaxed text-gray-800">
                {!! nl2br(e($lesson->lesson_text)) !!}
            </div>
        </div>


        {{-- ===== GRAMMAR SECTION ===== --}}
        @php $grammar = is_array($lesson->grammar_data) ? $lesson->grammar_data : []; @endphp
        @if(!empty($grammar))
            <section class="bg-indigo-50 rounded-2xl p-6 border-l-4 border-indigo-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-indigo-700 mb-4">✏️ {{ __('lessons.grammar') }}</h3>

                <div class="space-y-3">
                    @foreach($lesson->grammar_data as $index => $g)
                        <div x-data="{ open: false }" class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">

                            {{-- Заголовок карточки --}}
                            <button
                                    @click="open = !open"
                                    class="w-full flex justify-between items-center p-4 text-left font-semibold text-gray-800 hover:bg-indigo-50 transition"
                            >
                                <span>{{ $g['label'] ?? '—' }}</span>
                                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>

                            {{-- Содержание карточки --}}
                            <div x-show="open" x-transition class="p-4 border-t border-gray-100 text-sm text-gray-700 space-y-2">
                                {{-- Объяснение --}}
                                <p>{{ $g['explanation'] ?? '' }}</p>

                                {{-- Формулы --}}
                                @if(!empty($g['formula']))
                                    @php
                                        $formulaParts = is_array($g['formula']) ? $g['formula'] : [$g['formula']];
                                    @endphp
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($formulaParts as $part)
                                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-md">
                                    {{ $part }}
                                </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Пример --}}
                                @if(!empty($g['example']))
                                    <p class="mt-2 italic text-gray-600">“{{ $g['example'] }}”</p>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </section>
        @endif


        {{-- ===== VOCABULARY SECTION ===== --}}
        @php $vocab = is_array($lesson->vocabulary) ? $lesson->vocabulary : []; @endphp
        @if(!empty($vocab))
            <section class="bg-green-50 rounded-2xl p-6 border-l-4 border-green-400 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-semibold text-green-700">
                        🗣️ {{ __('lessons.vocabulary') }}
                    </h3>
                    <button id="toggleTranslations"
                            class="text-sm px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                        👁️ {{ __('lessons.show_translations') }}
                    </button>
                </div>

                <ul class="divide-y divide-green-100">
                    @foreach($vocab as $v)
                        <li class="py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <strong class="text-gray-800 text-lg">{{ $v['word'] ?? '' }}</strong>
                                    <span class="ml-2 text-sm text-gray-500 hidden translation">
                                        — {{ $v['translation'] ?? '' }}
                                    </span>
                                </div>
                                <button
                                        class="speak-btn text-gray-500 hover:text-green-700 transition"
                                        data-word="{{ $v['word'] }}">
                                    🔊
                                </button>
                            </div>
                            @if(!empty($v['example']))
                                <div class="text-sm italic text-gray-600 mt-1">{{ $v['example'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif


        {{-- ===== QUIZ SECTION ===== --}}
        @php $quiz = is_array($lesson->quiz) ? $lesson->quiz : []; @endphp
        @if(!empty($quiz))
            <section class="bg-yellow-50 rounded-2xl p-6 border-l-4 border-yellow-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-yellow-700 mb-4">🎯 Quiz</h3>
                <livewire:vocabulary-quiz :quiz="$quiz"/>
            </section>
        @endif

    </div>

    {{-- ===== INLINE SCRIPT ===== --}}
    <script>
        // Показать/скрыть переводы
        document.getElementById('toggleTranslations')?.addEventListener('click', () => {
            document.querySelectorAll('.translation').forEach(el => el.classList.toggle('hidden'));
        });

        // Озвучка слов (через SpeechSynthesis API)
        document.querySelectorAll('.speak-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const utterance = new SpeechSynthesisUtterance(btn.dataset.word);
                utterance.lang = '{{ strtolower($lesson->learning_lang ?? 'en') }}';
                speechSynthesis.speak(utterance);
            });
        });
    </script>
</x-app-layout>