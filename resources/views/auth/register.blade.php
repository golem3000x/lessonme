<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    {{ __('auth.register.title') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('auth.register.or_login') }}
                    <a href="{{ route('login') }}" class="font-semibold text-purple-600 hover:text-purple-700 transition">
                        {{ __('auth.register.login_link') }}
                    </a>
                </p>
            </div>

            {{-- Form card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form class="space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('auth.register.first_name_label') }}
                                </label>
                                <input id="first_name" name="first_name" type="text" autocomplete="given-name" required
                                       value="{{ old('first_name') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                       placeholder="{{ __('auth.register.first_name_placeholder') }}">
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('auth.register.last_name_label') }}
                                </label>
                                <input id="last_name" name="last_name" type="text" autocomplete="family-name" required
                                       value="{{ old('last_name') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                       placeholder="{{ __('auth.register.last_name_placeholder') }}">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('auth.register.email_label') }}
                            </label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                   placeholder="{{ __('auth.register.email_placeholder') }}">
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('auth.register.date_of_birth_label') }}
                            </label>
                            <input id="date_of_birth" name="date_of_birth" type="date" required
                                   value="{{ old('date_of_birth') }}"
                                   max="{{ now()->subYears(5)->format('Y-m-d') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('auth.register.password_label') }}
                            </label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                   placeholder="{{ __('auth.register.password_placeholder') }}">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('auth.register.password_confirmation_label') }}
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                   placeholder="{{ __('auth.register.password_confirmation_placeholder') }}">
                        </div>
                    </div>

                    <button type="submit"
                            class="group w-full flex items-center justify-center gap-2 py-3 px-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        {{ __('auth.register.submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
