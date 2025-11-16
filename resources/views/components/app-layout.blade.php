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

<nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 flex justify-between items-center h-16">
        <!-- Левая часть -->
        <div class="flex items-center gap-6">
            <a href="/" class="group text-lg font-bold text-primary hover:text-primary/80 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary drop-shadow-md transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ __('nav.app_name') }}
            </a>

            @auth
                <a href="/dashboard" class="group text-sm text-gray-600 hover:text-primary transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5-12h3m0 0l-2 2m2-2l-2-2"/>
                    </svg>
                    {{ __('nav.dashboard') }}
                </a>

                <a href="/lessons" class="group text-sm text-gray-600 hover:text-primary transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ __('nav.lessons') }}
                </a>

                <a href="/manage-topics" class="group text-sm text-gray-600 hover:text-primary transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                    </svg>
                    {{ __('nav.topics') }}
                </a>
            @endauth

            <a href="{{ route('about') }}" class="group text-sm text-gray-600 hover:text-primary transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('nav.about') }}
            </a>
        </div>

        <!-- Правая часть -->
        <div class="flex items-center gap-4">
            @auth
                <livewire:notification-bell />

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="group flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100 transition">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff"
                             class="h-8 w-8 rounded-full ring-2 ring-transparent group-hover:ring-blue-200 transition">
                        <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.away="open=false" x-transition
                         class="absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-lg shadow-lg py-2 z-50">
                        <a href="/onboarding"
                           class="group flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:scale-110"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M5.121 17.804A3 3 0 016 17h12a3 3 0 01.879.804M12 14a5 5 0 100-10 5 5 0 000 10z"/>
                            </svg>
                            {{ __('nav.profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="group flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-5 w-5 text-red-500 drop-shadow-sm transition-transform group-hover:translate-x-1"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                                </svg>
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="group text-sm text-gray-600 hover:text-primary flex items-center gap-1 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    {{ __('nav.login') }}
                </a>

                <a href="{{ route('register') }}"
                   class="group text-sm bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 flex items-center gap-1 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white drop-shadow-sm transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    {{ __('nav.register') }}
                </a>
            @endauth

            <!-- Переключатель языка -->
            @php($current_locale = app()->getLocale())
            @php($target_locale = $current_locale === 'vi' ? 'en' : 'vi')
            <a href="{{ route('locale.switch', $target_locale) }}"
               class="group text-sm font-medium text-gray-600 hover:text-primary transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 drop-shadow-sm transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9zm0 0c2.5 2.5 2.5 15.5 0 18m0-18c-2.5 2.5-2.5 15.5 0 18M3 12h18"/>
                </svg>
                {{ strtoupper($target_locale) }}
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