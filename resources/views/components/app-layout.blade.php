<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', __('nav.app_name')) }}</title>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen font-sans antialiased">
<nav class="bg-white shadow p-4">
    <div class="max-w-5xl mx-auto flex justify-between items-center">
        <a href="/" class="font-bold">{{ __('nav.app_name') }}</a>
        <div class="space-x-4 flex items-center">
            @auth
                <a href="/dashboard" class="text-sm">{{ __('nav.dashboard') }}</a>
                <a href="/chat" class="text-sm">{{ __('nav.chat') }}</a>
                <a href="/lessons" class="text-sm">{{ __('nav.lessons') }}</a>
                <a href="/onboarding" class="text-sm">{{ __('nav.profile') }}</a>
                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">{{ __('nav.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm">{{ __('nav.login') }}</a>
                <a href="{{ route('register') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">{{ __('nav.register') }}</a>
            @endauth
            @php($currentLocale = app()->getLocale())
            @php($targetLocale = $currentLocale === 'vi' ? 'en' : 'vi')
            <a href="{{ route('locale.switch', $targetLocale) }}" title="{{ __('nav.switch_to', ['lang' => $targetLocale === 'vi' ? __('nav.vietnamese') : __('nav.english')]) }}" class="text-sm inline-flex items-center" aria-label="{{ __('nav.switch_to', ['lang' => $targetLocale === 'vi' ? __('nav.vietnamese') : __('nav.english')]) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9zm0 0c2.5 2.5 2.5 15.5 0 18m0-18c-2.5 2.5-2.5 15.5 0 18M3 12h18" />
                </svg>
                {{ $targetLocale === 'vi' ? 'VI' : 'EN' }}
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
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
