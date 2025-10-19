<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'AI English Buddy') }}</title>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen font-sans antialiased">
<nav class="bg-white shadow p-4">
    <div class="max-w-5xl mx-auto flex justify-between items-center">
        <a href="/" class="font-bold">AI English Buddy</a>
        <div class="space-x-4 flex items-center">
            @auth
                <a href="/dashboard" class="text-sm">Dashboard</a>
                <a href="/chat" class="text-sm">Chat</a>
                <a href="/lessons" class="text-sm">Lessons</a>
                <a href="/onboarding" class="text-sm">Profile</a>
                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Выйти</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm">Вход</a>
                <a href="{{ route('register') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Регистрация</a>
            @endauth
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
