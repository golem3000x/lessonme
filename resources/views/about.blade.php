<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('nav.about') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Основная карточка -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <!-- Заголовок с градиентом -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ __('nav.about_title') }}</h1>
                            <p class="text-blue-100 mt-1">{{ __('nav.about_subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Содержимое -->
                <div class="p-8 space-y-8">
                    <!-- О проекте -->
                    <section class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 mb-3">{{ __('nav.about_what_title') }}</h2>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ __('nav.about_what_text') }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    <!-- Поддержка проекта -->
                    <section class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 mb-3">{{ __('nav.about_support_title') }}</h2>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ __('nav.about_support_text') }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    <!-- Планы развития -->
                    <section class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 mb-3">{{ __('nav.about_plans_title') }}</h2>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <p class="text-gray-600">{{ __('nav.about_plan_1') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <p class="text-gray-600">{{ __('nav.about_plan_2') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <p class="text-gray-600">{{ __('nav.about_plan_3') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <hr class="border-gray-200">

                    <!-- Связь -->
                    <section class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-white rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 mb-3">{{ __('nav.about_contact_title') }}</h2>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ __('nav.about_contact_text') }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Кнопка назад -->
                    <div class="pt-4 flex justify-center">
                        <a href="{{ url()->previous() }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            {{ __('nav.about_back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
