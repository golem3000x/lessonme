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
                    <span class="text-5xl">❓</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.faq') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.faq_description') }}</p>
            </div>

            <div class="space-y-6">
                {{-- General Questions --}}
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-blue-600 text-white px-6 py-3">
                        <h2 class="text-xl font-bold">General Questions</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">What is LessonMe?</h3>
                            <p class="text-gray-600">LessonMe is an interactive language learning platform that helps you learn English and Vietnamese through AI-generated lessons and various exercise types.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Is LessonMe free to use?</h3>
                            <p class="text-gray-600">Yes, LessonMe offers free access to its core features including lesson browsing, creation, and progress tracking.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">What languages are supported?</h3>
                            <p class="text-gray-600">Currently, LessonMe supports English and Vietnamese. You can learn either language and use either as your interface language.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('help.faq_languages_question') }}</h3>
                            <p class="text-gray-600">{{ __('help.faq_languages_answer') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Account & Settings --}}
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-green-600 text-white px-6 py-3">
                        <h2 class="text-xl font-bold">Account & Settings</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">How do I change my interface language?</h3>
                            <p class="text-gray-600">Use the language switcher in the navigation bar at the top of the page. Your choice will be saved for future visits.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">How do I change my learning language?</h3>
                            <p class="text-gray-600">Go to your Profile settings and update the "Learning Language" field. This determines which language you're studying.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Can I reset my progress?</h3>
                            <p class="text-gray-600">Your lesson scores are permanent to maintain an accurate learning history. However, you can retake any lesson as many times as you'd like to improve your score.</p>
                        </div>
                    </div>
                </div>

                {{-- Lessons & Learning --}}
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-purple-600 text-white px-6 py-3">
                        <h2 class="text-xl font-bold">Lessons & Learning</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">How are lessons created?</h3>
                            <p class="text-gray-600">Lessons are generated using AI based on topics and language levels. Each lesson includes a variety of exercise types to reinforce learning.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Can I create my own lessons?</h3>
                            <p class="text-gray-600">Yes! First create a topic, then use the AI lesson generator to create custom lessons based on your topic and preferred difficulty level.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">How is my score calculated?</h3>
                            <p class="text-gray-600">Your score is based on the percentage of correct answers across all exercises in a lesson. Each exercise type is weighted equally.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Can I retake a lesson?</h3>
                            <p class="text-gray-600">Absolutely! You can retake any lesson as many times as you want. Your best score will be highlighted on your dashboard.</p>
                        </div>
                    </div>
                </div>

                {{-- Technical Issues --}}
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-orange-600 text-white px-6 py-3">
                        <h2 class="text-xl font-bold">Technical Issues</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">The virtual keyboard isn't working</h3>
                            <p class="text-gray-600">Make sure JavaScript is enabled in your browser. If the issue persists, try refreshing the page or clearing your browser cache.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">My answers aren't being saved</h3>
                            <p class="text-gray-600">Ensure you have a stable internet connection. Your answers are saved when you click "Check Answers" at the end of the lesson.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">I'm not receiving notifications</h3>
                            <p class="text-gray-600">Check that you've granted notification permissions to the website. You can manage this in your browser settings.</p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
