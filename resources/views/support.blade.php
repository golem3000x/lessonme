<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('nav.about') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main card with two-column layout -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <!-- Header with gradient -->
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
                        </div>
                    </div>
                </div>

                <!-- Two-column Content -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 p-8">
                    <!-- Left column - Text content -->
                    <div class="lg:col-span-7 space-y-8">
                        <!-- About project -->
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

                        <!-- Project support -->
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

                        <!-- Development plans -->
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

                        <!-- Contact -->
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
                    </div>

                    <!-- Right column - QR Code -->
                    <div class="lg:col-span-5">
                        <div class="lg:sticky lg:top-24">
                            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8 shadow-lg border border-gray-100">
                                <!-- QR Code Image -->
                                <div class="bg-white rounded-xl p-6 shadow-md mb-6 overflow-hidden">
                                    <div class="overflow-hidden rounded-lg">
                                        <img src="{{ asset('images/napas-qr.jpg') }}" 
                                             alt="Napas QR Code" 
                                             class="w-full h-auto scale-105">
                                    </div>
                                </div>

                                <!-- PayPal Button Placeholder -->
                                <div class="space-y-3">
                                    <!-- This section is ready for PayPal button integration -->
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600 mb-3">{{ __('nav.support_methods') }}</p>
                                    </div>
                                    
                                    <!-- PayPal button will be placed here -->
                                    <div id="paypal-button-container" class="min-h-[50px]">
                                        <!-- PayPal SDK will render button here -->
                                        <button class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 px-6 rounded-lg shadow-md transition duration-200 flex items-center justify-center gap-2"
                                                onclick="alert('PayPal integration coming soon!')">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.067 8.478c.492.88.556 2.014.3 3.327-.74 3.806-3.276 5.12-6.514 5.12h-.5a.805.805 0 00-.794.68l-.04.22-.63 3.993-.028.15a.806.806 0 01-.795.68H7.72a.483.483 0 01-.477-.558L7.418 21h1.518l.95-6.02h1.385c4.678 0 7.75-2.203 8.796-6.502z"/>
                                                <path d="M2.379 21.582a.48.48 0 01-.477-.557l2.043-12.953a.955.955 0 01.942-.805h5.377c1.765 0 3.058.365 3.845 1.087.398.365.672.794.836 1.296.18.553.225 1.223.134 2.008a.24.24 0 01-.014.063l-.003.011a7.947 7.947 0 01-.457 1.342.24.24 0 00.013.197c.09.158.134.34.134.536 0 .179-.03.351-.09.513a2.463 2.463 0 01-1.504 1.594c-.653.232-1.446.348-2.362.348h-.476a.955.955 0 00-.942.805l-.717 4.544a.804.804 0 01-.795.68H2.379z"/>
                                            </svg>
                                            <span>PayPal (Coming Soon)</span>
                                        </button>
                                    </div>

                                    <!-- Support message -->
                                    <div class="mt-4 p-4 bg-white/50 rounded-lg">
                                        <p class="text-xs text-gray-600 text-center leading-relaxed">
                                            {{ __('nav.donation_help_text') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
