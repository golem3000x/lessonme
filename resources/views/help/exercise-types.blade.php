<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('help.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('help.back_to_help') }}
                </a>
            </div>

            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-5xl">🎯</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.exercise_types') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.exercise_types_description') }}</p>
            </div>

            <div class="space-y-8">
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">❓</span>
                        {{ __('help.quiz_exercises') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Multiple choice questions with one or more correct answers.</p>
                        <h3>How it works:</h3>
                        <ul>
                            <li>Read the question carefully</li>
                            <li>Select the answer(s) you think are correct</li>
                            <li>Some questions may have multiple correct answers</li>
                            <li>Click to select/deselect an option</li>
                        </ul>
                        <h3>Tips:</h3>
                        <ul>
                            <li>Eliminate obviously wrong answers first</li>
                            <li>Look for keywords in the question</li>
                            <li>Consider all options before finalizing</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">📝</span>
                        {{ __('help.gap_fill_exercises') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Fill in missing words or phrases in sentences.</p>
                        <h3>How it works:</h3>
                        <ul>
                            <li>Read the full sentence with gaps</li>
                            <li>Type the correct word(s) in each blank</li>
                            <li>Use the virtual keyboard for special characters</li>
                            <li>Pay attention to grammar and context</li>
                        </ul>
                        <h3>Tips:</h3>
                        <ul>
                            <li>Consider the sentence structure and tense</li>
                            <li>Watch for clues in surrounding words</li>
                            <li>Check spelling carefully</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">🔀</span>
                        {{ __('help.sentence_ordering_exercises') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Arrange scrambled words to form correct sentences.</p>
                        <h3>How it works:</h3>
                        <ul>
                            <li>Words are presented in random order</li>
                            <li>Drag and drop or click to arrange them</li>
                            <li>Form a grammatically correct sentence</li>
                            <li>Consider sentence structure rules</li>
                        </ul>
                        <h3>Tips:</h3>
                        <ul>
                            <li>Start with capital letters (usually sentence start)</li>
                            <li>Identify the subject and verb first</li>
                            <li>Look for logical word combinations</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">✓✗</span>
                        {{ __('help.true_false_exercises') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Determine whether statements are true or false.</p>
                        <h3>How it works:</h3>
                        <ul>
                            <li>Read each statement carefully</li>
                            <li>Decide if it's True or False</li>
                            <li>Select your answer</li>
                            <li>Consider all parts of the statement</li>
                        </ul>
                        <h3>Tips:</h3>
                        <ul>
                            <li>Watch for absolute words like "always" or "never"</li>
                            <li>If any part is false, the whole statement is false</li>
                            <li>Don't overthink - trust your knowledge</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">🔗</span>
                        {{ __('help.matching_exercises') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Match items from two columns that belong together.</p>
                        <h3>How it works:</h3>
                        <ul>
                            <li>Items are displayed in two columns</li>
                            <li>Drag from left column to matching item on right</li>
                            <li>Or select pairs by clicking</li>
                            <li>Each item matches exactly one other item</li>
                        </ul>
                        <h3>Tips:</h3>
                        <ul>
                            <li>Start with matches you're certain about</li>
                            <li>Process of elimination helps with harder ones</li>
                            <li>Look for contextual or grammatical clues</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
