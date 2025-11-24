<div class="space-y-6">
    {{-- EXERCISE COUNTER --}}
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg shadow-lg p-4">
        <div class="flex items-center justify-between">
            <div class="text-lg font-semibold">
                📚 {{ __('lessons.exercise_of', ['current' => $current_exercise_index + 1, 'total' => $total_exercises]) }}
            </div>
            <div class="text-sm opacity-90">
                {{ __('lessons.progress') }}: {{ round((($current_exercise_index + 1) / $total_exercises) * 100) }}%
            </div>
        </div>
        <div class="mt-2 bg-white bg-opacity-20 rounded-full h-2">
            <div class="bg-white rounded-full h-2 transition-all duration-300" 
                 style="width: {{ (($current_exercise_index + 1) / $total_exercises) * 100 }}%"></div>
        </div>
    </div>

    @if(!empty($all_exercises_flat))
        @php
            $current = $all_exercises_flat[$current_exercise_index];
            $type = $current['type'];
            $index = $current['index'];
            $item = $current['data'];
        @endphp

        {{-- CURRENT EXERCISE CARD --}}
        <div class="bg-white rounded-lg shadow-lg border-2 border-blue-200 p-8 min-h-[400px]">
            {{-- QUIZ --}}
            @if($type === 'quiz')
                <h4 class="text-xl font-bold mb-6 text-yellow-600 flex items-center gap-2">
                    <span class="text-2xl">🎯</span> {{ __('lessons.quiz') }}
                </h4>
                <div class="p-6 bg-yellow-50 rounded-lg">
                    <p class="text-gray-800 font-medium mb-4 text-lg">
                        {{ $item['question'] ?? '' }}
                    </p>

                    <div class="flex flex-col gap-3 mt-4">
                        @foreach($item['options'] ?? [] as $option)
                            <label class="cursor-pointer flex items-center gap-3 p-3 rounded-lg hover:bg-yellow-100 transition-colors">
                                <input
                                    type="radio"
                                    wire:model="user_answers.quiz.{{ $index }}"
                                    value="{{ substr($option, 0, 1) }}"
                                    class="w-4 h-4 text-yellow-600 focus:ring-yellow-500"
                                    @if($checked) disabled @endif
                                >
                                <span class="text-base">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if($checked)
                        <div class="text-sm mt-4 p-3 rounded-lg {{ ($user_answers['quiz'][$index] ?? '') === ($item['answer'] ?? '') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            @if(($user_answers['quiz'][$index] ?? '') === ($item['answer'] ?? ''))
                                <span class="font-medium">✅ {{ __('lessons.correct') }}</span>
                            @else
                                <span class="font-medium">❌ {{ __('lessons.wrong_answer') }} <strong>{{ $item['answer'] ?? '' }}</strong></span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- GAP FILL --}}
            @if($type === 'gap_fill')
                <div x-data="{
                    vnKeyboardOpen: false,
                    enKeyboardOpen: false,
                    showHint: false,
                    insertChar(char) {
                        const input = $refs.gapInput;
                        if (!input) return;
                        
                        const start = input.selectionStart;
                        const end = input.selectionEnd;
                        const currentValue = input.value || '';
                        
                        const newValue = currentValue.substring(0, start) + char + currentValue.substring(end);
                        input.value = newValue;
                        
                        // Update Livewire model
                        @this.set('user_answers.gap_fill.{{ $index }}', newValue);
                        
                        // Set cursor position after inserted character
                        const newPosition = start + char.length;
                        input.setSelectionRange(newPosition, newPosition);
                        input.focus();
                    }
                }">
                    <h4 class="text-xl font-bold mb-6 text-blue-600 flex items-center gap-2">
                        <span class="text-2xl">📝</span> {{ __('lessons.gap_fill') }}
                    </h4>
                    <div class="p-6 bg-blue-50 rounded-lg">
                        @php
                            $sentence = $item['sentence'] ?? '';
                            $parts = explode('___', $sentence);
                            // Extract hint from parentheses if exists
                            $hint = '';
                            if (preg_match('/\(([^)]+)\)/', $sentence, $matches)) {
                                $hint = $matches[1];
                                // Remove hint from sentence parts
                                $parts = array_map(function($part) {
                                    return preg_replace('/\s*\([^)]+\)/', '', $part);
                                }, $parts);
                            }
                        @endphp
                        
                        <div class="text-gray-800 text-xl leading-relaxed mb-4">
                            {{-- Speaker button for the complete sentence --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-600">{{ __('lessons.listen_and_fill') }}</span>
                                @php
                                    // Build complete sentence with the correct answer
                                    $complete_sentence = str_replace('___', $item['answer'] ?? '', $sentence);
                                    // Remove hint in parentheses
                                    $complete_sentence = preg_replace('/\s*\([^)]+\)/', '', $complete_sentence);
                                @endphp
                                <livewire:lesson.lesson-speaker 
                                    :word="$complete_sentence" 
                                    :lang="$learning_lang ?? 'vi'" 
                                    :key="'gap-speaker-' . $index" 
                                />
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                @foreach($parts as $part_index => $part)
                                    <span>{{ $part }}</span>
                                    @if($part_index < count($parts) - 1)
                                        <input
                                            x-ref="gapInput"
                                            type="text"
                                            wire:model="user_answers.gap_fill.{{ $index }}"
                                            placeholder="..."
                                            class="inline-block px-4 py-2 border-b-2 border-blue-400 bg-transparent text-center min-w-[150px] max-w-[250px] transition-all duration-200
                                                   @if(!$checked) focus:border-blue-600 focus:bg-blue-100 hover:border-blue-500 @endif
                                                   @if($checked) {{ isset($user_answers['gap_fill'][$index]) && strcasecmp(trim($user_answers['gap_fill'][$index]), trim($item['answer'] ?? '')) === 0 ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }} @endif"
                                            @if($checked) disabled @endif
                                        >
                                    @endif
                                @endforeach
                                
                                {{-- Hint Toggle Button --}}
                                @if(!$checked && !empty($hint))
                                    <button
                                        type="button"
                                        @click="showHint = !showHint"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium text-sm rounded-lg border border-blue-300 transition-colors ml-2"
                                    >
                                        <span class="text-base" x-show="!showHint">👁️</span>
                                        <span class="text-base" x-show="showHint">🙈</span>
                                        <span class="text-xs" x-text="showHint ? '{{ __('lessons.hide_hint') }}' : '{{ __('lessons.show_hint') }}'"></span>
                                    </button>
                                    <div 
                                        x-show="showHint"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-90"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-90"
                                        class="text-blue-700 font-medium px-3 py-2 bg-blue-200 rounded-lg"
                                    >
                                        ({{ $hint }})
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Virtual Keyboards --}}
                        @if(!$checked)
                            {{-- Vietnamese Keyboard (lesson in Vietnamese language) --}}
                            @if($learning_lang === 'vi')
                                <div class="mt-4">
                                    {{-- Toggle Button --}}
                                    <button
                                        type="button"
                                        @click="vnKeyboardOpen = !vnKeyboardOpen"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium text-sm rounded-lg border border-blue-300 transition-colors"
                                    >
                                        <span class="text-base">⌨️</span>
                                        <span class="text-xs">🇻🇳</span>
                                        <span>{{ __('lessons.vietnamese_keyboard') }}</span>
                                        <span
                                            class="text-xs transition-transform duration-200"
                                            :class="vnKeyboardOpen ? 'rotate-180' : ''"
                                        >▼</span>
                                    </button>

                                    {{-- Vietnamese Keyboard Panel --}}
                                    <div
                                        x-show="vnKeyboardOpen"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                                        x-transition:enter-end="opacity-100 transform translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 transform translate-y-0"
                                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                                        class="mt-2 p-4 bg-white rounded-lg border-2 border-blue-200 shadow-lg"
                                    >
                                        <div class="flex flex-wrap gap-1">
                                            @php
                                                $vietnamese_chars = [
                                                    ['á', 'à', 'ả', 'ã', 'ạ'],
                                                    ['ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ'],
                                                    ['â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ'],
                                                    ['é', 'è', 'ẻ', 'ẽ', 'ẹ'],
                                                    ['ê', 'ế', 'ề', 'ể', 'ễ', 'ệ'],
                                                    ['í', 'ì', 'ỉ', 'ĩ', 'ị'],
                                                    ['ó', 'ò', 'ỏ', 'õ', 'ọ'],
                                                    ['ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ'],
                                                    ['ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ'],
                                                    ['ú', 'ù', 'ủ', 'ũ', 'ụ'],
                                                    ['ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự'],
                                                    ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ'],
                                                    ['đ']
                                                ];
                                            @endphp
                                            @foreach($vietnamese_chars as $char_group)
                                                @foreach($char_group as $char)
                                                    <button
                                                        type="button"
                                                        @click="insertChar('{{ $char }}')"
                                                        class="px-2 py-1 min-w-[32px] bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium text-sm rounded border border-blue-300 transition-colors focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                                    >
                                                        {{ $char }}
                                                    </button>
                                                @endforeach
                                                <div class="w-2"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- English Keyboard (lesson in English language) --}}
                            @if($learning_lang === 'en')
                                <div class="mt-4">
                                    {{-- Toggle Button --}}
                                    <button
                                        type="button"
                                        @click="enKeyboardOpen = !enKeyboardOpen"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-medium text-sm rounded-lg border border-green-300 transition-colors"
                                    >
                                        <span class="text-base">⌨️</span>
                                        <span class="text-xs">🇬🇧</span>
                                        <span>{{ __('lessons.english_keyboard') }}</span>
                                        <span
                                            class="text-xs transition-transform duration-200"
                                            :class="enKeyboardOpen ? 'rotate-180' : ''"
                                        >▼</span>
                                    </button>

                                    {{-- English Keyboard Panel --}}
                                    <div
                                        x-show="enKeyboardOpen"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                                        x-transition:enter-end="opacity-100 transform translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 transform translate-y-0"
                                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                                        class="mt-2 p-4 bg-white rounded-lg border-2 border-green-200 shadow-lg"
                                    >
                                        <div class="flex flex-wrap gap-1">
                                            @php
                                                $english_chars = [
                                                    ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'],
                                                    ['k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't'],
                                                    ['u', 'v', 'w', 'x', 'y', 'z'],
                                                    ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                                                    ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'],
                                                    ['U', 'V', 'W', 'X', 'Y', 'Z']
                                                ];
                                            @endphp
                                            @foreach($english_chars as $char_group)
                                                @foreach($char_group as $char)
                                                    <button
                                                        type="button"
                                                        @click="insertChar('{{ $char }}')"
                                                        class="px-2 py-1 min-w-[32px] bg-green-100 hover:bg-green-200 text-green-800 font-medium text-sm rounded border border-green-300 transition-colors focus:ring-2 focus:ring-green-400 focus:outline-none"
                                                    >
                                                        {{ $char }}
                                                    </button>
                                                @endforeach
                                                <div class="w-2"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if($checked)
                            <div class="text-sm mt-4 p-3 rounded-lg {{ isset($user_answers['gap_fill'][$index]) && strcasecmp(trim($user_answers['gap_fill'][$index]), trim($item['answer'] ?? '')) === 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if(isset($user_answers['gap_fill'][$index]) && strcasecmp(trim($user_answers['gap_fill'][$index]), trim($item['answer'] ?? '')) === 0)
                                    <span class="font-medium">✅ {{ __('lessons.correct') }}</span>
                                @else
                                    <span class="font-medium">❌ {{ __('lessons.wrong_answer') }} <strong>{{ $item['answer'] ?? '' }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- SENTENCE ORDERING --}}
            @if($type === 'sentence_ordering')
                <h4 class="text-xl font-bold mb-6 text-purple-600 flex items-center gap-2">
                    <span class="text-2xl">🔄</span> {{ __('lessons.sentence_ordering') }}
                </h4>
                <div class="p-6 bg-purple-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-4">{{ __('lessons.arrange_words') }}</p>
                    <div class="flex flex-wrap gap-3 mb-4">
                        @foreach($ordered_words[$index] ?? [] as $word_index => $word)
                            <div class="flex items-center gap-1 bg-white border-2 border-purple-300 rounded-lg px-4 py-3 shadow-sm">
                                @if(!$checked)
                                    <button
                                        wire:click="moveWord({{ $index }}, {{ $word_index }}, 'left')"
                                        class="text-purple-600 hover:text-purple-800 px-2 py-1 rounded hover:bg-purple-100 transition-colors"
                                        @if($word_index === 0) disabled class="text-gray-300 cursor-not-allowed" @endif
                                    >
                                        ←
                                    </button>
                                @endif
                                <span class="font-medium text-gray-800 text-lg px-2">{{ $word }}</span>
                                @if(!$checked)
                                    <button
                                        wire:click="moveWord({{ $index }}, {{ $word_index }}, 'right')"
                                        class="text-purple-600 hover:text-purple-800 px-2 py-1 rounded hover:bg-purple-100 transition-colors"
                                        @if($word_index === count($ordered_words[$index] ?? []) - 1) disabled class="text-gray-300 cursor-not-allowed" @endif
                                    >
                                        →
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($checked)
                        @php
                            $user_sentence = implode(' ', $ordered_words[$index] ?? []);
                            $correct_sentence = trim($item['answer'] ?? '');
                            // Strip punctuation for comparison
                            $user_sentence_clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $user_sentence);
                            $correct_sentence_clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $correct_sentence);
                            $is_correct = strcasecmp(trim($user_sentence_clean), trim($correct_sentence_clean)) === 0;
                        @endphp
                        <div class="text-sm mt-4 p-3 rounded-lg {{ $is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            @if($is_correct)
                                <span class="font-medium">✅ {{ __('lessons.correct') }}</span>
                            @else
                                <span class="font-medium">❌ {{ __('lessons.wrong_answer') }} <strong>{{ $item['answer'] ?? '' }}</strong></span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- TRUE/FALSE --}}
            @if($type === 'true_false')
                <h4 class="text-xl font-bold mb-6 text-green-600 flex items-center gap-2">
                    <span class="text-2xl">✓</span> {{ __('lessons.true_false') }}
                </h4>
                <div class="p-6 bg-green-50 rounded-lg">
                    <p class="text-gray-800 mb-6 text-lg">{{ $item['statement'] ?? '' }}</p>
                    <div class="flex gap-4">
                        <label class="cursor-pointer flex items-center gap-3 p-4 bg-white rounded-lg border-2 border-green-300 hover:border-green-500 transition-colors flex-1">
                            <input
                                type="radio"
                                wire:model="user_answers.true_false.{{ $index }}"
                                value="True"
                                class="w-5 h-5 text-green-600 focus:ring-green-500"
                                @if($checked) disabled @endif
                            >
                            <span class="font-medium text-lg">{{ __('lessons.true') }}</span>
                        </label>
                        <label class="cursor-pointer flex items-center gap-3 p-4 bg-white rounded-lg border-2 border-red-300 hover:border-red-500 transition-colors flex-1">
                            <input
                                type="radio"
                                wire:model="user_answers.true_false.{{ $index }}"
                                value="False"
                                class="w-5 h-5 text-red-600 focus:ring-red-500"
                                @if($checked) disabled @endif
                            >
                            <span class="font-medium text-lg">{{ __('lessons.false') }}</span>
                        </label>
                    </div>
                    @if($checked)
                        <div class="text-sm mt-4 p-3 rounded-lg {{ isset($user_answers['true_false'][$index]) && strcasecmp($user_answers['true_false'][$index], $item['answer'] ?? '') === 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            @if(isset($user_answers['true_false'][$index]) && strcasecmp($user_answers['true_false'][$index], $item['answer'] ?? '') === 0)
                                <span class="font-medium">✅ {{ __('lessons.correct') }}</span>
                            @else
                                <span class="font-medium">❌ {{ __('lessons.wrong_answer') }} <strong>{{ $item['answer'] ?? '' }}</strong></span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- MATCHING --}}
            @if($type === 'matching')
                <h4 class="text-xl font-bold mb-6 text-orange-600 flex items-center gap-2">
                    <span class="text-2xl">🔗</span> {{ __('lessons.matching') }}
                </h4>
                <div class="p-6 bg-orange-50 rounded-lg">
                    <div class="flex flex-col gap-4">
                        <p class="text-gray-800 font-medium text-lg">{{ $item['word'] ?? '' }}</p>
                        <input
                            type="text"
                            wire:model="user_answers.matching.{{ $index }}"
                            placeholder="{{ __('lessons.match_with') }}"
                            class="px-4 py-3 border-2 rounded-lg w-full transition-all duration-200 text-base
                                   @if(!$checked) focus:ring-2 focus:ring-orange-400 focus:border-orange-400 hover:border-orange-300 border-orange-200 @endif
                                   @if($checked) {{ isset($user_answers['matching'][$index]) && strcasecmp(trim($user_answers['matching'][$index]), trim($item['match'] ?? '')) === 0 ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }} @endif"
                            @if($checked) disabled @endif
                        >
                        @if($checked)
                            <div class="text-sm mt-4 p-3 rounded-lg {{ isset($user_answers['matching'][$index]) && strcasecmp(trim($user_answers['matching'][$index]), trim($item['match'] ?? '')) === 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if(isset($user_answers['matching'][$index]) && strcasecmp(trim($user_answers['matching'][$index]), trim($item['match'] ?? '')) === 0)
                                    <span class="font-medium">✅ {{ __('lessons.correct') }}</span>
                                @else
                                    <span class="font-medium">❌ {{ __('lessons.wrong_answer') }} <strong>{{ $item['match'] ?? '' }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- NAVIGATION BUTTONS --}}
        <div class="flex items-center justify-between gap-4">
            <button
                wire:click="previousExercise"
                @if($current_exercise_index === 0) disabled @endif
                class="flex items-center gap-2 px-6 py-3 rounded-lg border-2 border-gray-300 hover:border-blue-500 hover:bg-blue-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:border-gray-300"
            >
                <span class="text-xl">←</span>
                <span class="font-medium">{{ __('lessons.previous') }}</span>
            </button>

            <button
                wire:click="nextExercise"
                @if($current_exercise_index >= $total_exercises - 1) disabled @endif
                class="flex items-center gap-2 px-6 py-3 rounded-lg border-2 border-gray-300 hover:border-blue-500 hover:bg-blue-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:border-gray-300"
            >
                <span class="font-medium">{{ __('lessons.next') }}</span>
                <span class="text-xl">→</span>
            </button>
        </div>
    @endif

    {{-- ACTION BUTTONS & SCORE --}}
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
        @if(!$checked)
            <button
                wire:click="showConfirmModal"
                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg w-full text-lg transition-colors shadow-lg hover:shadow-xl"
            >
                🎯 {{ __('lessons.check_answers') }}
            </button>
        @else
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">
                        {{ $total_score }} / {{ $max_score }}
                    </div>
                    <p class="text-gray-600 text-lg">
                        @if($total_score === $max_score)
                            🎉 {{ __('lessons.perfect_score') }}
                        @elseif($total_score >= $max_score * 0.8)
                            🌟 {{ __('lessons.great_job') }}
                        @elseif($total_score >= $max_score * 0.5)
                            👍 {{ __('lessons.good_effort') }}
                        @else
                            💪 {{ __('lessons.keep_practicing') }}
                        @endif
                    </p>
                </div>

                @if($total_score < $max_score)
                    <button
                        wire:click="resetExercises"
                        class="px-8 py-3 border-2 border-blue-600 text-blue-600 hover:bg-blue-50 font-bold rounded-lg w-full text-lg transition-colors"
                    >
                        🔄 {{ __('lessons.try_again') }}
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- CONFIRMATION MODAL --}}
    @if($show_confirm_modal)
        <x-confirm-modal-livewire
            icon="info"
            :title="__('lessons.check_answers_title')"
            :message="__('lessons.check_answers_message')"
            :confirmText="__('lessons.check_answers')"
            :cancelText="__('lessons.wait')"
            confirmMethod="checkAnswers"
            cancelMethod="closeConfirmModal"
        />
    @endif
</div>
