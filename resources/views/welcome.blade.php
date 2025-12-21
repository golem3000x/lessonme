<x-guest-layout>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 text-white overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative max-w-6xl mx-auto px-4 py-24 sm:py-32">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 tracking-tight">
                    {{ __('welcome.title') }}
                </h1>
                <p class="text-xl sm:text-2xl text-blue-100 mb-4 font-medium">
                    {{ __('welcome.subtitle') }}
                </p>
                <p class="text-lg text-blue-200 max-w-3xl mx-auto mb-10">
                    {{ __('welcome.description') }}
                </p>
                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        {{ __('welcome.sign_up') }}
                    </a>
                    <a href="#demo" class="px-8 py-4 bg-blue-500/30 text-white font-bold rounded-xl border-2 border-white/30 hover:bg-blue-500/50 transition-all">
                        {{ __('welcome.try_demo') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-gray-50 via-gray-50/50 to-transparent"></div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">{{ __('welcome.features_title') }}</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- AI Generation -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_ai_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_ai_desc') }}</p>
                </div>

                <!-- Complete Lessons -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_lessons_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_lessons_desc') }}</p>
                </div>

                <!-- CEFR Levels -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_levels_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_levels_desc') }}</p>
                </div>

                <!-- Languages -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_languages_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_languages_desc') }}</p>
                </div>

                <!-- Courses -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_courses_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_courses_desc') }}</p>
                </div>

                <!-- For Teachers -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_teachers_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_teachers_desc') }}</p>
                </div>

                <!-- For Students -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_students_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_students_desc') }}</p>
                </div>

                <!-- Audio -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-fuchsia-500 to-purple-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_audio_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_audio_desc') }}</p>
                </div>

                <!-- Find Teachers/Students -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('welcome.feature_search_title') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ __('welcome.feature_search_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">{{ __('welcome.how_it_works_title') }}</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
                        <span class="text-3xl font-bold text-blue-600">1</span>
                        <div class="hidden md:block absolute left-full top-1/2 w-full h-0.5 bg-blue-200"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('welcome.step_1_title') }}</h3>
                    <p class="text-gray-600">{{ __('welcome.step_1_desc') }}</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 bg-indigo-100 rounded-full mb-6">
                        <span class="text-3xl font-bold text-indigo-600">2</span>
                        <div class="hidden md:block absolute left-full top-1/2 w-full h-0.5 bg-indigo-200"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('welcome.step_2_title') }}</h3>
                    <p class="text-gray-600">{{ __('welcome.step_2_desc') }}</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-6">
                        <span class="text-3xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('welcome.step_3_title') }}</h3>
                    <p class="text-gray-600">{{ __('welcome.step_3_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Lesson Creator Section -->
    <div id="demo" class="py-20 bg-gradient-to-br from-gray-100 to-gray-200">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">{{ __('welcome.try_demo') }}</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full"></div>
            </div>
            @livewire('welcome.demo-lesson-creator')
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative py-20 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white via-white/30 to-transparent"></div>
        <div class="relative max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">{{ __('welcome.ready_to_start') }}</h2>
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">{{ __('welcome.register_now') }}</p>
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="{{ route('register') }}" class="px-10 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    {{ __('welcome.sign_up') }}
                </a>
                <a href="{{ route('login') }}" class="px-10 py-4 bg-blue-500/30 text-white font-bold rounded-xl border-2 border-white/30 hover:bg-blue-500/50 transition-all">
                    {{ __('welcome.sign_in') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
