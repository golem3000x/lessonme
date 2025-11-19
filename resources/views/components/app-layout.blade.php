<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="user-id" content="{{ Auth::id() }}">
    <title>{{ config('app.name', __('nav.app_name')) }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Опционально: кастомная тема -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#2563EB' } // Синий
                }
            }
        }
    </script>

    <!-- Alpine.js x-cloak -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen font-sans antialiased">

<nav class="bg-gradient-to-r from-white via-gray-50 to-white border-b border-gray-200 sticky top-0 z-50 shadow-lg backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
        <!-- Левая часть -->
        <div class="flex items-center gap-8">
            <a href="/" class="group text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent hover:from-blue-700 hover:to-indigo-700 transition-all flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md group-hover:shadow-lg transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white transition-transform group-hover:rotate-12" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="hidden sm:inline">{{ __('nav.app_name') }}</span>
            </a>

            @auth
                <a href="/dashboard" class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->is('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                    @if(request()->is('dashboard'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5-12h3m0 0l-2 2m2-2l-2-2"/>
                    </svg>
                    <span class="hidden md:inline">{{ __('nav.dashboard') }}</span>
                </a>

                <a href="/lessons" class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->is('lessons*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    @if(request()->is('lessons*'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="hidden md:inline">{{ __('nav.lessons') }}</span>
                </a>

                <a href="/manage-topics" class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->is('manage-topics*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    @if(request()->is('manage-topics*'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="hidden md:inline">{{ __('nav.topics') }}</span>
                </a>
            @endauth

            <a href="{{ route('about') }}" class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-600' : '' }}">
                @if(request()->routeIs('about'))
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="hidden md:inline">{{ __('nav.about') }}</span>
            </a>
        </div>

        <!-- Правая часть -->
        <div class="flex items-center gap-4">
            @auth
                <livewire:notification-bell />

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-all duration-200">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                 alt="{{ Auth::user()->name }}"
                                 class="h-9 w-9 rounded-full ring-2 ring-gray-200 group-hover:ring-blue-400 transition-all shadow-sm object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff"
                                 alt="{{ Auth::user()->name }}"
                                 class="h-9 w-9 rounded-full ring-2 ring-gray-200 group-hover:ring-blue-400 transition-all shadow-sm">
                        @endif
                        <span class="hidden md:inline text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-all duration-200" :class="{'rotate-180 text-blue-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.away="open=false" x-transition
                         class="absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-xl shadow-2xl py-2 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                            <div class="flex items-center gap-3 mb-2">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                         alt="{{ Auth::user()->name }}"
                                         class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff"
                                         alt="{{ Auth::user()->name }}"
                                         class="h-10 w-10 rounded-full border-2 border-white shadow-sm">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="/onboarding"
                           class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all">
                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            {{ __('nav.profile') }}
                        </a>

                        <div class="border-t border-gray-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="group flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all">
                                <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 text-red-600 transition-transform group-hover:translate-x-0.5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                                    </svg>
                                </div>
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="group px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg flex items-center gap-2 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    {{ __('nav.login') }}
                </a>

                <a href="{{ route('register') }}"
                   class="group px-5 py-2 text-sm font-medium bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg flex items-center gap-2 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    {{ __('nav.register') }}
                </a>
            @endauth

            <!-- Переключатель языка -->
            @php($current_locale = app()->getLocale())
            @php($target_locale = $current_locale === 'vi' ? 'en' : 'vi')
            @php($flag = $target_locale === 'vi' ? '🇻🇳' : '🇬🇧')
            <a href="{{ route('locale.switch', $target_locale) }}"
               class="group px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg flex items-center gap-2 transition-all duration-200"
               title="{{ __('nav.switch_language') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:rotate-180 duration-500" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="hidden sm:inline">{{ $flag }} {{ strtoupper($target_locale) }}</span>
                <span class="sm:hidden">{{ $flag }}</span>
            </a>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto mt-6 p-4">
    {{ $slot }}
</main>

<!-- Real-time notifications panel -->
@auth
    <livewire:notifications-panel />
@endauth

<!-- Chat button - только на странице About -->
@auth
    @if(request()->routeIs('about'))
        <livewire:chat-button />
        <livewire:chat-panel />
    @endif
@endauth

@livewireScripts
@vite(['resources/js/app.js'])
</body>
</html>