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
                    <span class="text-5xl">📋</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.courses') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.courses_description') }}</p>
            </div>

            <div class="space-y-8">
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.courses_what_is') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.courses_what_is_text') }}</p>
                        <ul>
                            <li>{{ __('help.courses_feature_1') }}</li>
                            <li>{{ __('help.courses_feature_2') }}</li>
                            <li>{{ __('help.courses_feature_3') }}</li>
                            <li>{{ __('help.courses_feature_4') }}</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.courses_for_teachers') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.courses_for_teachers_text') }}</p>
                        <ol>
                            <li>{{ __('help.courses_step_1') }}</li>
                            <li>{{ __('help.courses_step_2') }}</li>
                            <li>{{ __('help.courses_step_3') }}</li>
                            <li>{{ __('help.courses_step_4') }}</li>
                            <li>{{ __('help.courses_step_5') }}</li>
                        </ol>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.courses_for_students') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.courses_for_students_text') }}</p>
                        <ul>
                            <li>{{ __('help.courses_student_1') }}</li>
                            <li>{{ __('help.courses_student_2') }}</li>
                            <li>{{ __('help.courses_student_3') }}</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.courses_passing_score') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>{{ __('help.courses_passing_score_text') }}</p>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>
