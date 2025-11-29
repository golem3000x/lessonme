<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    {{ __('auth.login.title') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('auth.login.or_register') }}
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition">
                        {{ __('auth.login.register_link') }}
                    </a>
                </p>
            </div>

            {{-- Form card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
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
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('auth.login.email_label') }}
                            </label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   placeholder="{{ __('auth.login.email_placeholder') }}">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('auth.login.password_label') }}
                            </label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   placeholder="{{ __('auth.login.password_placeholder') }}">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition">
                            <label for="remember" class="ml-2 block text-sm text-gray-700 font-medium">
                                {{ __('auth.login.remember_me') }}
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-700 transition">
                                {{ __('auth.login.forgot_password') }}
                            </a>
                        </div>
                    </div>

                    <button type="submit"
                            class="group w-full flex items-center justify-center gap-2 py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="h-5 w-5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('auth.login.submit') }}
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">{{ __('auth.login.or_continue_with') }}</span>
                    </div>
                </div>

                {{-- Social buttons --}}
                <div class="space-y-3">
                    <a href="{{ route('facebook.login') }}"
                       class="group w-full flex items-center justify-center gap-3 bg-[#1877F2] text-white px-4 py-3 rounded-lg hover:bg-[#166FE5] font-medium shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>{{ __('auth.login.sign_in_with_facebook') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
