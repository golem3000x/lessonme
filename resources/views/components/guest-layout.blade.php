<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
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
        </div>

        <!-- Правая часть -->
        <div class="flex items-center gap-4">
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

<main>
    {{ $slot }}
</main>

<footer class="bg-gray-900 text-gray-300 py-8 mt-16">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p>&copy; {{ date('Y') }} {{ __('nav.app_name') }}. {{ __('welcome.all_rights_reserved') }}</p>
    </div>
</footer>

@livewireScripts
@vite(['resources/js/app.js'])

{{-- Remove Facebook OAuth fragment #_=_ from URL --}}
<script>
    if (window.location.hash === '#_=_') {
        history.replaceState 
            ? history.replaceState(null, null, window.location.href.split('#')[0])
            : window.location.hash = '';
    }
</script>
</body>
</html>
