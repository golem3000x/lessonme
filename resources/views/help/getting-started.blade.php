<x-app-layout>
    <div class="max-w-4xl space-y-6">
            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('help.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('help.back_to_help') }}
                </a>
            </div>

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-5xl">🚀</span>
                    <h1 class="text-4xl font-bold text-gray-900">{{ __('help.getting_started') }}</h1>
                </div>
                <p class="text-lg text-gray-600">{{ __('help.getting_started_description') }}</p>
            </div>

            {{-- Content Sections --}}
            <div class="space-y-8">
                {{-- Welcome Tour --}}
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">🎓</span>
                        {{ __('help.welcome_tour') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>The Welcome Tour is an interactive guide that helps you familiarize yourself with the LessonMe platform. It walks you through the main features and interface elements.</p>
                        <h3>What you'll learn:</h3>
                        <ul>
                            <li>How to navigate the Dashboard</li>
                            <li>Where to find Lessons and Topics</li>
                            <li>How to use the Notifications panel</li>
                            <li>Accessing your Profile settings</li>
                        </ul>
                        <button 
                            onclick="window.startTour()"
                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Start Tour
                        </button>
                    </div>
                </section>

                {{-- Account Setup --}}
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">⚙️</span>
                        {{ __('help.account_setup') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>Set up your account to personalize your learning experience on LessonMe.</p>
                        <h3>Steps to configure your account:</h3>
                        <ol>
                            <li><strong>Profile Settings:</strong> Click your profile avatar in the top-right corner and select "Profile" to update your personal information.</li>
                            <li><strong>Learning Language:</strong> Select which language you're learning (English or Vietnamese) in your profile settings.</li>
                            <li><strong>Interface Language:</strong> Choose your preferred interface language using the language switcher in the navigation bar.</li>
                            <li><strong>Notifications:</strong> Customize your notification preferences to stay updated on lesson progress and assignments.</li>
                        </ol>
                    </div>
                </section>

                {{-- Understanding Levels --}}
                <section class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="text-3xl">📊</span>
                        {{ __('help.understanding_levels') }}
                    </h2>
                    <div class="prose prose-blue max-w-none">
                        <p>LessonMe uses the Common European Framework of Reference (CEFR) for language levels.</p>
                        <h3>Language Levels:</h3>
                        <ul>
                            <li><strong>A1 - Beginner:</strong> Basic phrases and simple expressions</li>
                            <li><strong>A2 - Elementary:</strong> Everyday expressions and simple conversations</li>
                            <li><strong>B1 - Intermediate:</strong> Familiar topics and basic work situations</li>
                            <li><strong>B2 - Upper Intermediate:</strong> Complex topics and technical discussions</li>
                            <li><strong>C1 - Advanced:</strong> Demanding texts and implicit meanings</li>
                            <li><strong>C2 - Proficient:</strong> Native-like comprehension and expression</li>
                        </ul>
                        <p>Your dashboard shows statistics broken down by level, helping you track progress across different difficulty ranges.</p>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>
