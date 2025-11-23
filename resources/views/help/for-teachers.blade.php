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
                    <span class="text-5xl">✍️</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.for_teachers') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.for_teachers_description') }}</p>
            </div>

            <div class="space-y-8">
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.creating_topics') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Topics help organize lessons by subject matter and difficulty level.</p>
                        <ul>
                            <li>Navigate to <strong>Topics</strong> in the menu</li>
                            <li>Click "Create Topic" button</li>
                            <li>Enter topic name, description, and select language level</li>
                            <li>Add relevant keywords to help categorize the topic</li>
                            <li>Save and start creating lessons for this topic</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.generating_lessons') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Use AI to automatically generate comprehensive lessons with various exercise types.</p>
                        <ul>
                            <li>Select a topic from your topics list</li>
                            <li>Click "Generate Lesson" or navigate to Lessons → Create</li>
                            <li>Choose lesson parameters (level, exercise types, difficulty)</li>
                            <li>The AI will generate exercises automatically</li>
                            <li>Review and edit generated content if needed</li>
                            <li>Publish the lesson for students</li>
                        </ul>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.understanding_grading') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>English Buddy uses a comprehensive grading system based on accuracy and completeness.</p>
                        <h3>Grading Scale:</h3>
                        <ul>
                            <li><strong>A (90-100%):</strong> Excellent understanding</li>
                            <li><strong>B (80-89%):</strong> Good performance</li>
                            <li><strong>C (70-79%):</strong> Satisfactory</li>
                            <li><strong>D (60-69%):</strong> Needs improvement</li>
                            <li><strong>F (0-59%):</strong> Further study required</li>
                        </ul>
                        <p>Grades are calculated automatically based on correct answers across all exercise types.</p>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('help.managing_content') }}</h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Keep your lessons and topics organized and up-to-date.</p>
                        <ul>
                            <li>View lesson history to see all versions and changes</li>
                            <li>Restore previous versions if needed</li>
                            <li>Check student scores to identify areas for improvement</li>
                            <li>Update topics and lessons based on student feedback</li>
                            <li>Delete outdated content to keep your library fresh</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
