<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', __('nav.app_name')) }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: custom theme -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#2563EB' } // Blue
                }
            }
        }
    </script>

    <!-- Alpine.js x-cloak -->
    <style>
        [x-cloak] { display: none !important; }
        /* Twemoji emoji sizing */
        img.emoji {
            height: 1.2em;
            width: 1.2em;
            margin: 0 .05em 0 .1em;
            vertical-align: -0.15em;
            display: inline-block;
        }
    </style>
    <!-- Twemoji for consistent flag rendering across all locales -->
    <script src="https://cdn.jsdelivr.net/npm/twemoji@14.0.2/dist/twemoji.min.js" crossorigin="anonymous"></script>

    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen font-sans antialiased">

<nav class="bg-gradient-to-r from-white via-gray-50 to-white border-b border-gray-200 sticky top-0 z-50 shadow-lg backdrop-blur-md" x-data="{ mobileMenuOpen: false }">
    @php($current_locale = app()->getLocale())
    @php($languages = \App\Enums\System\SystemLanguageEnum::all())
    @php($currentFlag = \App\Enums\System\SystemLanguageEnum::flagByValue($current_locale))
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex justify-between items-center h-16">
        <!-- Mobile menu button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="sm:hidden p-2 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all"
                :class="{ 'bg-blue-50 text-blue-600': mobileMenuOpen }">
            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Left side - Logo -->
        <div class="flex items-center gap-8">
            <a href="/" class="group text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent hover:from-blue-700 hover:to-indigo-700 transition-all flex items-center gap-2">
                <div class="p-1.5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md group-hover:shadow-lg transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="hidden sm:inline">{{ __('nav.app_name') }}</span>
            </a>
        </div>

        <!-- Right side - Desktop -->
        <div class="flex items-center gap-2 sm:gap-4">
            <!-- Login - hidden on very small screens -->
            <a href="{{ route('login') }}" class="hidden sm:flex group px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg items-center gap-2 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('nav.login') }}
            </a>

            <!-- Register -->
            <a href="{{ route('register') }}"
               class="group px-3 sm:px-5 py-2 text-sm font-medium bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg flex items-center gap-2 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="hidden sm:inline">{{ __('nav.register') }}</span>
            </a>

            <!-- Language switcher - hidden on mobile -->
            <div class="relative hidden sm:block" x-data="{ open: false }">
                <button @click="open = !open"
                        class="group px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg flex items-center gap-2 transition-all duration-200"
                        title="{{ __('nav.switch_language') }}">
                    <span>{{ $currentFlag }}</span>
                    <span class="uppercase font-semibold">{{ $current_locale }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="open" 
                     x-cloak
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    @foreach($languages as $language)
                        <a href="{{ route('locale.switch', $language->value) }}"
                           class="flex items-center gap-2 px-4 py-2 text-sm {{ $current_locale === $language->value ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition-colors">
                            <span>{{ $language->flag() }}</span>
                            <span>{{ $language->label() }}</span>
                            @if($current_locale === $language->value)
                                <svg class="w-4 h-4 ml-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div x-show="mobileMenuOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-gray-200 bg-white shadow-lg">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('login') }}" 
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('nav.login') }}
            </a>
            <a href="{{ route('register') }}" 
               @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('nav.register') }}
            </a>

            {{-- Language Switcher for Mobile --}}
            <div class="px-4 py-2 mt-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('nav.switch_language') }}</span>
            </div>
            <div class="flex flex-wrap gap-2 px-4 py-2">
                @foreach($languages as $language)
                    <a href="{{ route('locale.switch', $language->value) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ $current_locale === $language->value ? 'bg-blue-100 text-blue-600 font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                        <span>{{ $language->flag() }}</span>
                        <span class="uppercase">{{ $language->value }}</span>
                    </a>
                @endforeach
            </div>
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

{{-- Parse emoji flags with Twemoji for consistent rendering --}}
<script>
    function parseEmoji() {
        if (typeof twemoji !== 'undefined') {
            twemoji.parse(document.body, {
                folder: 'svg',
                ext: '.svg',
                className: 'emoji'
            });
        }
    }
    document.addEventListener('DOMContentLoaded', parseEmoji);
</script>
</body>
</html>
