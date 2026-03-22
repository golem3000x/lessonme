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
                    <span class="text-5xl">📚</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.for_students') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.for_students_description') }}</p>
            </div>

            <div class="space-y-8">
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.browsing_lessons') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Browse and filter available lessons to find content that matches your learning level and interests.</p>
                        <ul>
                            <li>Click <strong>Lessons</strong> in the navigation bar</li>
                            <li>Use filters to find lessons by level (A1-C2) or topic</li>
                            <li>Click on a lesson card to view details and start learning</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.taking_lesson') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Complete exercises one by one, using the navigation buttons to move forward and backward.</p>
                        <ul>
                            <li>Read each exercise carefully</li>
                            <li>Use virtual keyboards when needed for special characters</li>
                            <li>Click "Check Answers" when ready to submit</li>
                            <li>Review your results and see correct answers</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.tracking_progress') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Monitor your learning journey through the Dashboard statistics.</p>
                        <ul>
                            <li><strong>Completed Lessons:</strong> Total number of lessons you've finished</li>
                            <li><strong>Average Score:</strong> Your overall performance average</li>
                            <li><strong>Best Score:</strong> Your highest achievement</li>
                            <li><strong>Recent Scores:</strong> Chart showing your recent attempts</li>
                            <li><strong>Level Statistics:</strong> Breakdown of lessons by difficulty level</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.virtual_keyboards') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Use on-screen keyboards to type special characters in your target language.</p>
                        <ul>
                            <li>Virtual keyboards appear automatically during gap-fill exercises</li>
                            <li>Click on characters to insert them into your answer</li>
                            <li>Vietnamese learners see English keyboard, English learners see Vietnamese keyboard</li>
                        </ul>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>
