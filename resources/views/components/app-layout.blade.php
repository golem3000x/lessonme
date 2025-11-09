@php use App\Enums\SystemLanguageEnum; @endphp
        <!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>{{ config('app.name', __('nav.app_name')) }}</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen font-sans antialiased">
<nav class="bg-white shadow p-4">
    <div class="max-w-5xl mx-auto flex justify-between items-center">
        <!-- Левая часть: логотип + меню -->
        <div class="flex items-center space-x-6">
            <a href="/" class="font-bold text-gray-900">{{ __('nav.app_name') }}</a>

            @auth
                <a href="/dashboard" class="text-sm text-gray-700 hover:text-gray-900">
                    {{ __('nav.dashboard') }}
                </a>
                <a href="/lessons" class="text-sm text-gray-700 hover:text-gray-900">
                    {{ __('nav.lessons') }}
                </a>
                <a href="/manage-topics" class="text-sm text-gray-700 hover:text-gray-900">
                    {{ __('nav.topics') }}
                </a>
            @endauth
        </div>

        <!-- Правая часть: уведомления, профиль, язык -->
        <div class="flex items-center space-x-4">
            @auth
                @livewire('notification-bell')

                {{-- User Dropdown Menu --}}
                <div x-data="{ open: false }" class="relative" @click.away="open = false">
                    <button @click="open = !open" class="text-sm text-gray-600 hover:text-gray-800 flex items-center focus:outline-none">
                        {{ Auth::user()->name }}
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                         style="display: none;">

                        <a href="/onboarding" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('nav.profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm">{{ __('nav.login') }}</a>
                <a href="{{ route('register') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    {{ __('nav.register') }}
                </a>
            @endauth

            {{-- Переключатель языка --}}
            @php($current_locale = app()->getLocale())
            @php($vi = SystemLanguageEnum::VIETNAMESE->value)
            @php($en = SystemLanguageEnum::ENGLISH->value)
            @php($target_locale = $current_locale === $vi ? $en : $vi)
            <a href="{{ route('locale.switch', $target_locale) }}"
               title="{{ __('nav.switch_to', ['lang' => $target_locale === $vi ? __('nav.vietnamese') : __('nav.english')]) }}"
               class="text-sm inline-flex items-center"
               aria-label="{{ __('nav.switch_to', ['lang' => $target_locale === $vi ? __('nav.vietnamese') : __('nav.english')]) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9zm0 0c2.5 2.5 2.5 15.5 0 18m0-18c-2.5 2.5-2.5 15.5 0 18M3 12h18"/>
                </svg>
                {{ $target_locale === $vi ? 'VI' : 'EN' }}
            </a>
        </div>
    </div>
</nav>


@if (isset($header))
    <header class="bg-white shadow">
        <div class="max-w-5xl mx-auto py-6 px-4">
            {{ $header }}
        </div>
    </header>
@endif

<main>
    @livewire('notifications-panel')
    {{ $slot }}
</main>

@livewireScripts
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@vite(['resources/js/app.js'])
</body>
</html>