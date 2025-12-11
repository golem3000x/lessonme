@php use App\Enums\System\LanguageLevelEnum; @endphp

<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('lessons.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('lessons.create_lesson_title') }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="saveDraft" class="px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    {{ __('lessons.save_draft') }}
                </button>
                <button wire:click="publishLesson" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-medium shadow-md transition">
                    {{ __('lessons.publish_lesson') }}
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto" aria-label="Tabs">
                    @php
                        $tabs = [
                            'basic' => ['label' => __('lessons.basic_info'), 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'vocabulary' => ['label' => __('lessons.vocabulary'), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                            'grammar' => ['label' => __('lessons.grammar'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            'dialogue' => ['label' => __('lessons.dialogue'), 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                            'exercises' => ['label' => __('lessons.exercises'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ];
                    @endphp
                    @foreach($tabs as $key => $tab)
                        <button wire:click="setActiveTab('{{ $key }}')"
                                class="flex items-center gap-2 px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition
                                    {{ $active_tab === $key 
                                        ? 'border-blue-500 text-blue-600 bg-blue-50/50' 
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                            </svg>
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="p-6">
                {{-- Basic Info Tab --}}
                @if($active_tab === 'basic')
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Lesson Name --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('lessons.lesson_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="lesson_name" 
                                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="{{ __('lessons.lesson_name_placeholder') }}">
                                @error('lesson_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Language --}}
                            @if(count($available_languages) > 1)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('lessons.lesson_language') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="lesson_language" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($available_languages as $lang)
                                        <option value="{{ $lang['value'] }}">{{ $lang['flag'] }} {{ $lang['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Level --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('lessons.level') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="language_level" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(LanguageLevelEnum::cases() as $level)
                                        <option value="{{ $level->value }}">{{ $level->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Topic --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('lessons.topic_label') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="topic_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic['id'] }}">{{ $topic['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lesson Text --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('lessons.lesson_description') }}
                                </label>
                                <textarea wire:model="lesson_text" rows="4"
                                          class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          placeholder="{{ __('lessons.lesson_description_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Vocabulary Tab --}}
                @if($active_tab === 'vocabulary')
                    <div class="space-y-4">
                        @foreach($vocabulary as $index => $item)
                            <div class="flex gap-4 items-start p-4 bg-gray-50 rounded-lg" wire:key="vocab-{{ $index }}">
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input type="text" wire:model="vocabulary.{{ $index }}.word" 
                                           class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                           placeholder="{{ __('lessons.word') }}">
                                    <input type="text" wire:model="vocabulary.{{ $index }}.translation" 
                                           class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                           placeholder="{{ __('lessons.translation') }}">
                                    <input type="text" wire:model="vocabulary.{{ $index }}.example" 
                                           class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                           placeholder="{{ __('lessons.example_sentence') }}">
                                </div>
                                <button wire:click="removeVocabularyItem({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                        
                        <button wire:click="addVocabularyItem" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('lessons.add_vocabulary') }}
                        </button>
                    </div>
                @endif

                {{-- Grammar Tab --}}
                @if($active_tab === 'grammar')
                    <div class="space-y-4">
                        @foreach($grammar_data as $index => $item)
                            <div class="p-4 bg-gray-50 rounded-lg space-y-3" wire:key="grammar-{{ $index }}">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">{{ __('lessons.grammar_rule') }} #{{ $index + 1 }}</span>
                                    <button wire:click="removeGrammarItem({{ $index }})" class="p-1 text-red-500 hover:bg-red-50 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <input type="text" wire:model="grammar_data.{{ $index }}.phrase" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="{{ __('lessons.phrase') }}">
                                    <input type="text" wire:model="grammar_data.{{ $index }}.label" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="{{ __('lessons.label') }}">
                                    <input type="text" wire:model="grammar_data.{{ $index }}.formula" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="{{ __('lessons.formula') }}">
                                    <input type="text" wire:model="grammar_data.{{ $index }}.example" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="{{ __('lessons.example') }}">
                                    <textarea wire:model="grammar_data.{{ $index }}.explanation" class="border border-gray-300 rounded-lg px-3 py-2 md:col-span-2" rows="2" placeholder="{{ __('lessons.explanation') }}"></textarea>
                                </div>
                            </div>
                        @endforeach
                        
                        <button wire:click="addGrammarItem" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('lessons.add_grammar') }}
                        </button>
                    </div>
                @endif

                {{-- Dialogue Tab --}}
                @if($active_tab === 'dialogue')
                    <div class="space-y-3">
                        @foreach($dialogue as $index => $line)
                            <div class="flex gap-3 items-center" wire:key="dialogue-{{ $index }}">
                                <select wire:model="dialogue.{{ $index }}.speaker" class="w-20 border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                                <input type="text" wire:model="dialogue.{{ $index }}.line" 
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                                       placeholder="{{ __('lessons.dialogue_line') }}">
                                <button wire:click="removeDialogueLine({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                        
                        <button wire:click="addDialogueLine" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('lessons.add_dialogue_line') }}
                        </button>
                    </div>
                @endif

                {{-- Exercises Tab --}}
                @if($active_tab === 'exercises')
                    <div x-data="{ exerciseTab: 'gap_fill' }" class="space-y-4">
                        {{-- Exercise sub-tabs --}}
                        <div class="flex flex-wrap gap-2 pb-4 border-b">
                            <button @click="exerciseTab = 'gap_fill'" 
                                    :class="exerciseTab === 'gap_fill' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                Gap Fill ({{ count($gap_fill) }}/7)
                            </button>
                            <button @click="exerciseTab = 'sentence_ordering'"
                                    :class="exerciseTab === 'sentence_ordering' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                Word Order ({{ count($sentence_ordering) }}/6)
                            </button>
                            <button @click="exerciseTab = 'true_false'"
                                    :class="exerciseTab === 'true_false' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                True/False ({{ count($true_false) }}/7)
                            </button>
                            <button @click="exerciseTab = 'matching'"
                                    :class="exerciseTab === 'matching' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                Matching ({{ count($matching) }}/9)
                            </button>
                            <button @click="exerciseTab = 'quiz'"
                                    :class="exerciseTab === 'quiz' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                                Quiz ({{ count($quiz) }}/6)
                            </button>
                        </div>

                        {{-- Gap Fill --}}
                        <div x-show="exerciseTab === 'gap_fill'" class="space-y-3">
                            <p class="text-sm text-gray-600">{{ __('lessons.gap_fill_hint') }}</p>
                            @foreach($gap_fill as $index => $item)
                                <div class="flex gap-3 items-center" wire:key="gap-{{ $index }}">
                                    <input type="text" wire:model="gap_fill.{{ $index }}.sentence" 
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="I ___ to school every day">
                                    <input type="text" wire:model="gap_fill.{{ $index }}.answer" 
                                           class="w-32 border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="go">
                                    <button wire:click="removeGapFill({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                            @if(count($gap_fill) < 7)
                                <button wire:click="addGapFill" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('lessons.add_exercise') }}
                                </button>
                            @endif
                        </div>

                        {{-- Sentence Ordering --}}
                        <div x-show="exerciseTab === 'sentence_ordering'" class="space-y-3">
                            <p class="text-sm text-gray-600">{{ __('lessons.word_order_hint') }}</p>
                            @foreach($sentence_ordering as $index => $item)
                                <div class="flex gap-3 items-center" wire:key="order-{{ $index }}">
                                    <input type="text" wire:model="sentence_ordering.{{ $index }}.answer" 
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="{{ __('lessons.correct_sentence') }}">
                                    <button wire:click="removeSentenceOrdering({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                            @if(count($sentence_ordering) < 6)
                                <button wire:click="addSentenceOrdering" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('lessons.add_exercise') }}
                                </button>
                            @endif
                        </div>

                        {{-- True/False --}}
                        <div x-show="exerciseTab === 'true_false'" class="space-y-3">
                            @foreach($true_false as $index => $item)
                                <div class="flex gap-3 items-center" wire:key="tf-{{ $index }}">
                                    <input type="text" wire:model="true_false.{{ $index }}.statement" 
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="{{ __('lessons.statement') }}">
                                    <select wire:model="true_false.{{ $index }}.answer" class="w-28 border border-gray-300 rounded-lg px-3 py-2">
                                        <option value="True">True</option>
                                        <option value="False">False</option>
                                    </select>
                                    <button wire:click="removeTrueFalse({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                            @if(count($true_false) < 7)
                                <button wire:click="addTrueFalse" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('lessons.add_exercise') }}
                                </button>
                            @endif
                        </div>

                        {{-- Matching --}}
                        <div x-show="exerciseTab === 'matching'" class="space-y-3">
                            @foreach($matching as $index => $item)
                                <div class="flex gap-3 items-center" wire:key="match-{{ $index }}">
                                    <input type="text" wire:model="matching.{{ $index }}.word" 
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="{{ __('lessons.word') }}">
                                    <span class="text-gray-400">→</span>
                                    <input type="text" wire:model="matching.{{ $index }}.match" 
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="{{ __('lessons.translation') }}">
                                    <button wire:click="removeMatching({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                            @if(count($matching) < 9)
                                <button wire:click="addMatching" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('lessons.add_exercise') }}
                                </button>
                            @endif
                        </div>

                        {{-- Quiz --}}
                        <div x-show="exerciseTab === 'quiz'" class="space-y-4">
                            @foreach($quiz as $index => $item)
                                <div class="p-4 bg-gray-50 rounded-lg space-y-3" wire:key="quiz-{{ $index }}">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium">{{ __('lessons.question') }} #{{ $index + 1 }}</span>
                                        <button wire:click="removeQuiz({{ $index }})" class="p-1 text-red-500 hover:bg-red-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="text" wire:model="quiz.{{ $index }}.question" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                           placeholder="{{ __('lessons.question') }}">
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach(['A', 'B', 'C', 'D'] as $optIndex => $opt)
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-500">{{ $opt }}.</span>
                                                <input type="text" wire:model="quiz.{{ $index }}.options.{{ $optIndex }}" 
                                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600">{{ __('lessons.correct_answer') }}:</span>
                                        <select wire:model="quiz.{{ $index }}.answer" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                            @if(count($quiz) < 6)
                                <button wire:click="addQuiz" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('lessons.add_question') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
