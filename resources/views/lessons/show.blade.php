<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-10">

        {{-- ===== HEADER / INTRO ===== --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-start justify-between mb-3">
                <h2 class="text-3xl font-bold text-indigo-700">
                    📘 {{ $lesson->lesson_name }}
                </h2>
                @auth
                    @if(auth()->id() === $lesson->user_id || auth()->user()->isAdmin())
                        <a href="{{ route('lessons.scores', $lesson->uuid) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            {{ __('lessons.view_scores') }}
                        </a>
                    @endif
                @endauth
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-500 mb-6">
                @if($lesson->lesson_lang)
                    <span class="text-2xl" title="{{ $lesson->lesson_lang->label() }}">
                        {{ $lesson->lesson_lang->flag() }}
                    </span>
                @endif
                
                @if($lesson->language_level)
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $lesson->level_badge_color }}">
                        {{ $lesson->language_level->label() }}
                    </span>
                @endif
                <span>{{ __('lessons.topic') }}: {{ $lesson->topic?->title() ?? '—' }}</span>
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        <div x-data="{ open: false }"
                             class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">

                            {{-- Card header --}}
                            <button
                                    @click="open = !open"
                                    class="w-full flex justify-between items-center p-4 text-left font-semibold text-gray-800 hover:bg-indigo-50 transition"
                            >
                                <span>{{ $g['label'] ?? '—' }}</span>
                                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>

                            {{-- Card content --}}
                            <div x-show="open" x-transition
                                 class="p-4 border-t border-gray-100 text-sm text-gray-700 space-y-4">

                                {{-- Grammar Phrase --}}
                                @if(!empty($g['phrase']))
                                    <div class="bg-indigo-100 border-l-4 border-indigo-500 p-3 rounded">
                                        <p class="text-xs uppercase text-indigo-600 font-semibold mb-1">Grammar
                                            Pattern:</p>
                                        <p class="text-lg font-bold text-indigo-900">{{ $g['phrase'] }}</p>
                                    </div>
                                @endif

                                {{-- Explanation --}}
                                @if(!empty($g['explanation']))
                                    <p class="text-gray-700">{{ $g['explanation'] }}</p>
                                @endif

                                {{-- Formula --}}
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

                                {{-- Breakdown --}}
                                @if(!empty($g['breakdown']))
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                                        <p class="text-xs uppercase text-yellow-700 font-semibold mb-1">Breakdown:</p>
                                        <p class="text-gray-800 whitespace-pre-line">
                                            {{ $g['breakdown'] }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Example --}}
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
            <section x-data="{ showTranslations: false }" class="bg-green-50 rounded-2xl p-6 border-l-4 border-green-400 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-semibold text-green-700">
                        🗣️ {{ __('lessons.vocabulary') }}
                    </h3>
                    <button 
                        @click="showTranslations = !showTranslations"
                        class="flex items-center gap-2 text-sm px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition"
                    >
                        <span x-show="!showTranslations">👁️</span>
                        <span x-show="showTranslations">🙈</span>
                        <span x-text="showTranslations ? '{{ __('lessons.hide_translations') }}' : '{{ __('lessons.show_translations') }}'"></span>
                    </button>
                </div>

                <ul class="divide-y divide-green-100">
                    @foreach($vocab as $v)
                        <li class="py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <strong class="text-gray-800 text-lg">{{ $v['word'] ?? '' }}</strong>
                                    <span 
                                        x-show="showTranslations" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 transform scale-100"
                                        x-transition:leave-end="opacity-0 transform scale-95"
                                        class="ml-2 text-sm text-green-700 font-medium"
                                    >
                                        — {{ $v['translation'] ?? '' }}
                                    </span>
                                </div>
                                @php
                                    $audio_file = $lesson->getVocabularyAudio($v['word'] ?? '');
                                    $audio_url = $audio_file?->getPublicUrl();
                                @endphp
                                <livewire:lesson.lesson-speaker 
                                    :audio_url="$audio_url" 
                                    :key="'speaker-vocab-' . $loop->index" 
                                />
                            </div>
                            @if(!empty($v['example']))
                                <div class="text-sm italic text-gray-600 mt-1">{{ $v['example'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif


        {{-- ===== DIALOGUE SECTION ===== --}}
        @php $dialogue = is_array($lesson->dialogue) ? $lesson->dialogue : []; @endphp
        @if(!empty($dialogue))
            <section class="bg-purple-50 rounded-2xl p-6 border-l-4 border-purple-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-purple-700 mb-4">💬 {{ __('lessons.dialogue') }}</h3>

                <div class="space-y-4">
                    @foreach($dialogue as $line)
                        <div class="flex gap-3">
                            <span class="font-semibold text-purple-700">{{ $line['speaker'] ?? '—' }}:</span>
                            <span class="text-gray-800">{{ $line['line'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif


        {{-- ===== EXERCISES SECTION ===== --}}
        @php $ex = is_array($lesson->exercises) ? $lesson->exercises : []; @endphp
        @if(!empty($ex))
            <section class="bg-blue-50 rounded-2xl p-6 border-l-4 border-blue-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-blue-700 mb-6">📝 {{ __('lessons.exercises') }}</h3>
                <livewire:lesson.lesson-exercises :exercises="$ex" :lesson_id="$lesson->id"/>
            </section>
        @endif


        {{-- ===== HOMEWORK SECTION ===== --}}
        @if(!empty($lesson->homework))
            <section class="bg-orange-50 rounded-2xl p-6 border-l-4 border-orange-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-orange-700 mb-3">📚 {{ __('lessons.homework') }}</h3>
                <p class="text-gray-800 leading-relaxed">
                    {!! nl2br(e($lesson->homework)) !!}
                </p>
            </section>
        @endif


        {{-- ===== COMMON MISTAKES SECTION ===== --}}
        @php $mistakes = is_array($lesson->common_mistakes) ? $lesson->common_mistakes : []; @endphp
        @if(!empty($mistakes))
            <section class="bg-red-50 rounded-2xl p-6 border-l-4 border-red-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-red-700 mb-4">⚠️ {{ __('lessons.common_mistakes') }}</h3>

                <div class="space-y-4">
                    @foreach($mistakes as $m)
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <p class="text-red-600 font-semibold">❌ {{ $m['mistake'] ?? '' }}</p>
                            <p class="text-green-700 font-semibold">✔ {{ $m['correction'] ?? '' }}</p>
                            <p class="text-gray-700 mt-1">{{ $m['explanation'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif


        {{-- ===== CULTURE NOTES SECTION ===== --}}
        @if(!empty($lesson->culture_notes))
            <section class="bg-indigo-50 rounded-2xl p-6 border-l-4 border-indigo-400 shadow-sm">
                <h3 class="text-2xl font-semibold text-indigo-700 mb-3">🌍 {{ __('lessons.culture_notes') }}</h3>
                <p class="text-gray-800 leading-relaxed">
                    {!! nl2br(e($lesson->culture_notes)) !!}
                </p>
            </section>
        @endif


        {{-- ===== FEEDBACK SECTION ===== --}}
        @auth
            @if(!auth()->user()->is_temp_guest)
                <section class="mt-8">
                    <livewire:lesson.lesson-feedback-form :lesson_id="$lesson->id"/>
                </section>
            @endif
        @endauth

    </div>
</x-app-layout>