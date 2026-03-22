<x-app-layout>
    <div class="space-y-6 sm:space-y-8">
            {{-- Header --}}
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-3">
                    {{ __('help.help_center') }}
                </h1>
                <p class="text-lg text-gray-600">
                    {{ __('help.help_description') }}
                </p>
            </div>

            {{-- Interactive Tour Card --}}
            <div class="mb-10">
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-xl shadow-2xl p-8 text-white relative overflow-hidden">
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <pattern id="grid-help" width="40" height="40" patternUnits="userSpaceOnUse">
                                <circle cx="20" cy="20" r="1" fill="white"/>
                            </pattern>
                            <rect width="100%" height="100%" fill="url(#grid-help)"/>
                        </svg>
                    </div>

                    <div class="flex items-center gap-4 mb-4 relative z-10">
                        <span class="text-6xl help-icon-pulse drop-shadow-lg">🎓</span>
                        <div class="flex-1">
                            <h2 class="text-3xl font-bold mb-2 drop-shadow-md">{{ __('help.restart_tour') }}</h2>
                            <p class="text-blue-50 text-lg">{{ __('help.restart_tour_description') }}</p>
                        </div>
                    </div>
                    <button 
                        onclick="window.startTour()"
                        class="btn-shine inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 font-semibold rounded-lg hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 transform relative z-10">
                        <svg class="w-5 h-5 transition-transform hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('help.restart_tour') }}
                    </button>
                </div>
            </div>

            {{-- Help Categories Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Getting Started --}}
                <a href="{{ route('help.getting-started') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-blue-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">🚀</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                            {{ __('help.getting_started') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.getting_started_description') }}</p>
                </a>

                {{-- For Students --}}
                <a href="{{ route('help.for-students') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-green-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">📚</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">
                            {{ __('help.for_students') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.for_students_description') }}</p>
                </a>

                {{-- For Teachers --}}
                <a href="{{ route('help.for-teachers') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-purple-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">✍️</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors">
                            {{ __('help.for_teachers') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.for_teachers_description') }}</p>
                </a>

                {{-- Exercise Types --}}
                <a href="{{ route('help.exercise-types') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-orange-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">🎯</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                            {{ __('help.exercise_types') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.exercise_types_description') }}</p>
                </a>

                {{-- Courses --}}
                <a href="{{ route('help.courses') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-cyan-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">📋</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-cyan-600 transition-colors">
                            {{ __('help.courses') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.courses_description') }}</p>
                </a>

                {{-- Find Users --}}
                <a href="{{ route('help.find-users') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-teal-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-teal-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">🔍</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-teal-600 transition-colors">
                            {{ __('help.find_users') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.find_users_description') }}</p>
                </a>

                {{-- AI Generation --}}
                <a href="{{ route('help.ai-generation') }}" class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-indigo-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">🤖</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                            {{ __('help.ai_generation') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.ai_generation_description') }}</p>
                </a>

                {{-- FAQ --}}
                <a href="{{ route('help.faq') }}" 
                   class="help-card group bg-white rounded-xl shadow-lg border-2 border-gray-200 hover:border-pink-500 hover:shadow-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-pink-100 to-transparent rounded-bl-full opacity-50"></div>
                    <div class="flex items-center gap-3 mb-3 relative z-10">
                        <span class="help-card-icon text-5xl">❓</span>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-pink-600 transition-colors">
                            {{ __('help.faq') }}
                        </h3>
                    </div>
                    <p class="text-gray-600 relative z-10">{{ __('help.faq_description') }}</p>
                </a>
            </div>

            {{-- Chat Contact Badge --}}
            <div class="mt-10 flex justify-center">
                <div class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
                    <span class="text-3xl">💬</span>
                    <p class="text-gray-700 font-medium">
                        {{ __('help.still_have_questions') }}
                    </p>
                </div>
            </div>
    </div>
</x-app-layout>
