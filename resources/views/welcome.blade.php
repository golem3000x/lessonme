<x-guest-layout>
    <!-- Hero Section -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4">
        <div class="max-w-6xl w-full">
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold mb-6 text-gray-900">{{ __('welcome.title') }}</h1>
                <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                    {{ __('welcome.description') }}
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-4xl mb-4">🎯</div>
                    <h2 class="text-xl font-bold mb-3 text-gray-900">{{ __('welcome.feature_1_title') }}</h2>
                    <p class="text-gray-600">{{ __('welcome.feature_1_desc') }}</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-4xl mb-4">🤖</div>
                    <h2 class="text-xl font-bold mb-3 text-gray-900">{{ __('welcome.feature_2_title') }}</h2>
                    <p class="text-gray-600">{{ __('welcome.feature_2_desc') }}</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-4xl mb-4">📚</div>
                    <h2 class="text-xl font-bold mb-3 text-gray-900">{{ __('welcome.feature_3_title') }}</h2>
                    <p class="text-gray-600">{{ __('welcome.feature_3_desc') }}</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="text-4xl mb-4">🌍</div>
                    <h2 class="text-xl font-bold mb-3 text-gray-900">{{ __('welcome.feature_4_title') }}</h2>
                    <p class="text-gray-600">{{ __('welcome.feature_4_desc') }}</p>
                </div>
            </div>

            <div class="text-center">
                <div class="inline-block w-full max-w-md border-t-2 border-gray-300 mb-4"></div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('welcome.try_demo') }}</h2>
                <div class="inline-block w-full max-w-md border-t-2 border-gray-300 mt-4"></div>
            </div>
        </div>
    </div>

    <!-- Demo Lesson Creator Section -->
    <div class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            @livewire('welcome.demo-lesson-creator')
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6 text-gray-900">{{ __('welcome.ready_to_start') }}</h2>
            <p class="text-xl mb-8 text-gray-700">{{ __('welcome.register_now') }}</p>
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="{{ route('register') }}" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                    {{ __('welcome.sign_up') }}
                </a>
                <a href="{{ route('login') }}" class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg border-2 border-blue-600 hover:bg-blue-50 transition-colors shadow-md hover:shadow-lg">
                    {{ __('welcome.sign_in') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
