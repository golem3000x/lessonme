<div class="space-y-8">
    {{-- GAP FILL EXERCISES --}}
    @if(!empty($exercises['gap_fill']))
        <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6">
            <h4 class="text-lg font-semibold mb-4 text-blue-600">📝 Fill in the blanks</h4>
            <div class="space-y-4">
                @foreach($exercises['gap_fill'] as $index => $item)
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-800">{{ $item['sentence'] ?? '' }}</p>
                            <input
                                type="text"
                                wire:model="user_answers.gap_fill.{{ $index }}"
                                placeholder="Your answer..."
                                class="input input-bordered w-full max-w-md transition-all duration-200 
                                       @if(!$checked) focus:ring-2 focus:ring-blue-400 focus:border-blue-400 hover:border-blue-300 @endif
                                       @if($checked) {{ isset($user_answers['gap_fill'][$index]) && strcasecmp(trim($user_answers['gap_fill'][$index]), trim($item['answer'] ?? '')) === 0 ? 'input-success' : 'input-error' }} @endif"
                                @if($checked) disabled @endif
                            >
                            @if($checked)
                                <div class="text-sm mt-2">
                                    @if(isset($user_answers['gap_fill'][$index]) && strcasecmp(trim($user_answers['gap_fill'][$index]), trim($item['answer'] ?? '')) === 0)
                                        <span class="text-success font-medium">✅ Correct!</span>
                                    @else
                                        <span class="text-error font-medium">❌ Wrong. Correct answer: <strong>{{ $item['answer'] ?? '' }}</strong></span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- SENTENCE ORDERING EXERCISES --}}
    @if(!empty($exercises['sentence_ordering']))
        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
            <h4 class="text-lg font-semibold mb-4 text-purple-600">🔄 Put in the correct order</h4>
            <div class="space-y-6">
                @foreach($exercises['sentence_ordering'] as $index => $item)
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-3">Arrange the words in the correct order:</p>
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($ordered_words[$index] ?? [] as $word_index => $word)
                                <div class="flex items-center gap-1 bg-white border border-purple-300 rounded-lg px-3 py-2">
                                    @if(!$checked)
                                        <button
                                            wire:click="moveWord({{ $index }}, {{ $word_index }}, 'left')"
                                            class="btn btn-xs btn-circle btn-ghost"
                                            @if($word_index === 0) disabled @endif
                                        >
                                            ←
                                        </button>
                                    @endif
                                    <span class="font-medium text-gray-800">{{ $word }}</span>
                                    @if(!$checked)
                                        <button
                                            wire:click="moveWord({{ $index }}, {{ $word_index }}, 'right')"
                                            class="btn btn-xs btn-circle btn-ghost"
                                            @if($word_index === count($ordered_words[$index] ?? []) - 1) disabled @endif
                                        >
                                            →
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($checked)
                            <div class="text-sm mt-2">
                                @php
                                    $user_sentence = implode(' ', $ordered_words[$index] ?? []);
                                    $correct_sentence = trim($item['answer'] ?? '');
                                    $is_correct = strcasecmp($user_sentence, $correct_sentence) === 0;
                                @endphp
                                @if($is_correct)
                                    <span class="text-success font-medium">✅ Correct!</span>
                                @else
                                    <span class="text-error font-medium">❌ Wrong. Correct answer: <strong>{{ $item['answer'] ?? '' }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- TRUE/FALSE EXERCISES --}}
    @if(!empty($exercises['true_false']))
        <div class="bg-white rounded-lg shadow-sm border border-green-100 p-6">
            <h4 class="text-lg font-semibold mb-4 text-green-600">✓ True or False</h4>
            <div class="space-y-4">
                @foreach($exercises['true_false'] as $index => $item)
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-gray-800 mb-3">{{ $item['statement'] ?? '' }}</p>
                        <div class="flex gap-3">
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    wire:model="user_answers.true_false.{{ $index }}"
                                    value="True"
                                    class="radio radio-success radio-sm"
                                    @if($checked) disabled @endif
                                >
                                <span class="ml-2">True</span>
                            </label>
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    wire:model="user_answers.true_false.{{ $index }}"
                                    value="False"
                                    class="radio radio-error radio-sm"
                                    @if($checked) disabled @endif
                                >
                                <span class="ml-2">False</span>
                            </label>
                        </div>
                        @if($checked)
                            <div class="text-sm mt-3">
                                @if(isset($user_answers['true_false'][$index]) && strcasecmp($user_answers['true_false'][$index], $item['answer'] ?? '') === 0)
                                    <span class="text-success font-medium">✅ Correct!</span>
                                @else
                                    <span class="text-error font-medium">❌ Wrong. Correct answer: <strong>{{ $item['answer'] ?? '' }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- MATCHING EXERCISES --}}
    @if(!empty($exercises['matching']))
        <div class="bg-white rounded-lg shadow-sm border border-orange-100 p-6">
            <h4 class="text-lg font-semibold mb-4 text-orange-600">🔗 Match the pairs</h4>
            <div class="space-y-4">
                @foreach($exercises['matching'] as $index => $item)
                    <div class="p-4 bg-orange-50 rounded-lg">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-800 font-medium">{{ $item['word'] ?? '' }}</p>
                            <input
                                type="text"
                                wire:model="user_answers.matching.{{ $index }}"
                                placeholder="Match with..."
                                class="input input-bordered w-full max-w-md transition-all duration-200 
                                       @if(!$checked) focus:ring-2 focus:ring-orange-400 focus:border-orange-400 hover:border-orange-300 @endif
                                       @if($checked) {{ isset($user_answers['matching'][$index]) && strcasecmp(trim($user_answers['matching'][$index]), trim($item['match'] ?? '')) === 0 ? 'input-success' : 'input-error' }} @endif"
                                @if($checked) disabled @endif
                            >
                            @if($checked)
                                <div class="text-sm mt-2">
                                    @if(isset($user_answers['matching'][$index]) && strcasecmp(trim($user_answers['matching'][$index]), trim($item['match'] ?? '')) === 0)
                                        <span class="text-success font-medium">✅ Correct!</span>
                                    @else
                                        <span class="text-error font-medium">❌ Wrong. Correct answer: <strong>{{ $item['match'] ?? '' }}</strong></span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ACTION BUTTONS & SCORE --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @if(!$checked)
            <button
                wire:click="checkAnswers"
                class="btn btn-primary btn-lg w-full"
            >
                🎯 Check Answers
            </button>
        @else
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">
                        {{ $total_score }} / {{ $max_score }}
                    </div>
                    <p class="text-gray-600">
                        @if($total_score === $max_score)
                            🎉 Perfect score! Excellent work!
                        @elseif($total_score >= $max_score * 0.8)
                            🌟 Great job! Keep it up!
                        @elseif($total_score >= $max_score * 0.5)
                            👍 Good effort! Practice more!
                        @else
                            💪 Keep practicing, you'll get better!
                        @endif
                    </p>
                </div>

                @if($total_score < $max_score)
                    <button
                        wire:click="resetExercises"
                        class="btn btn-outline btn-primary w-full"
                    >
                        🔄 Try Again
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
