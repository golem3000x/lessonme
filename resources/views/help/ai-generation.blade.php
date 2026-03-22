<x-app-layout>
    <div class="max-w-4xl space-y-6">
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
                    <span class="text-5xl">🤖</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.ai_generation') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.ai_generation_description') }}</p>
            </div>

            <div class="space-y-8">
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.ai_how_it_works') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.ai_how_it_works_text') }}</p>
                        <ol>
                            <li>{{ __('help.ai_step_1') }}</li>
                            <li>{{ __('help.ai_step_2') }}</li>
                            <li>{{ __('help.ai_step_3') }}</li>
                            <li>{{ __('help.ai_step_4') }}</li>
                        </ol>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.ai_lesson_contains') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.ai_lesson_contains_text') }}</p>
                        <ul>
                            <li><strong>{{ __('help.ai_vocabulary') }}:</strong> {{ __('help.ai_vocabulary_text') }}</li>
                            <li><strong>{{ __('help.ai_grammar') }}:</strong> {{ __('help.ai_grammar_text') }}</li>
                            <li><strong>{{ __('help.ai_dialogue') }}:</strong> {{ __('help.ai_dialogue_text') }}</li>
                            <li><strong>{{ __('help.ai_exercises') }}:</strong> {{ __('help.ai_exercises_text') }}</li>
                            <li><strong>{{ __('help.ai_homework') }}:</strong> {{ __('help.ai_homework_text') }}</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.ai_cefr_levels') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.ai_cefr_levels_text') }}</p>
                        <ul>
                            <li><strong>A1-A2:</strong> {{ __('help.ai_level_beginner') }}</li>
                            <li><strong>B1-B2:</strong> {{ __('help.ai_level_intermediate') }}</li>
                            <li><strong>C1-C2:</strong> {{ __('help.ai_level_advanced') }}</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.ai_exercise_types') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.ai_exercise_types_text') }}</p>
                        <ul>
                            <li><strong>{{ __('help.ai_gap_fill') }}:</strong> {{ __('help.ai_gap_fill_text') }}</li>
                            <li><strong>{{ __('help.ai_matching') }}:</strong> {{ __('help.ai_matching_text') }}</li>
                            <li><strong>{{ __('help.ai_true_false') }}:</strong> {{ __('help.ai_true_false_text') }}</li>
                            <li><strong>{{ __('help.ai_sentence_order') }}:</strong> {{ __('help.ai_sentence_order_text') }}</li>
                            <li><strong>{{ __('help.ai_quiz') }}:</strong> {{ __('help.ai_quiz_text') }}</li>
                        </ul>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>
