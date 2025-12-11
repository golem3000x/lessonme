<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="user-id" content="{{ Auth::id() }}">
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
    </style>

    @livewireStyles
</head>

<body class="bg-gray-50 min-h-screen font-sans antialiased">

<nav class="bg-gradient-to-r from-white via-gray-50 to-white border-b border-gray-200 sticky top-0 z-50 shadow-lg backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
        <!-- Left side -->
        <div class="flex items-center gap-4">
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19h16M7 16V9m5 7V5m5 11v-6" />
                    </svg>
                    <span class="hidden md:inline">{{ __('nav.dashboard') }}</span>
                </a>

                {{-- Education Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->is('lessons*') || request()->is('manage-topics*') || request()->is('courses*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        @if(request()->is('lessons*') || request()->is('manage-topics*') || request()->is('courses*'))
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="hidden md:inline">{{ __('nav.education') }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-all duration-200" :class="{'rotate-180 text-blue-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.away="open=false" x-transition
                         class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl py-2 z-50 overflow-hidden">
                        <a href="/lessons"
                           class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all {{ request()->is('lessons*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            {{ __('nav.lessons') }}
                        </a>
                        <a href="/manage-topics"
                           class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all {{ request()->is('manage-topics*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            {{ __('nav.topics') }}
                        </a>
                        <a href="{{ route('courses.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all {{ request()->is('courses*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            {{ __('nav.courses') }}
                        </a>
                    </div>
                </div>

                {{-- Community Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->is('users*') || request()->is('profile*') || request()->is('messages*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        @if(request()->is('users*') || request()->is('profile*') || request()->is('messages*'))
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="hidden md:inline">{{ __('nav.community') }}</span>
                        @livewire('messages.unread-messages-badge')
                        <svg class="w-4 h-4 text-gray-500 transition-all duration-200" :class="{'rotate-180 text-blue-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.away="open=false" x-transition
                         class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl py-2 z-50 overflow-hidden">
                        <a href="{{ route('users.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all {{ request()->is('users*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            {{ __('nav.users') }}
                        </a>
                        @livewire('messages.unread-messages-dropdown-item')
                    </div>
                </div>

                <a href="{{ route('help.index') }}" class="group relative px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 flex items-center gap-2 {{ request()->is('help*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    @if(request()->is('help*'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="hidden md:inline">{{ __('nav.help') }}</span>
                </a>
            @endauth
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-8">
            @auth
                <div class="notifications-panel">
                    <livewire:notification.notification-bell />
                </div>

                <div x-data="{ open: false }" class="user-menu-dropdown relative">
                    <button @click="open = !open"
                            class="group flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-all duration-200">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar_url }}"
                                 alt="{{ Auth::user()->full_name }}"
                                 class="h-9 w-9 rounded-full ring-2 ring-gray-200 group-hover:ring-blue-400 transition-all shadow-sm object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=2563EB&color=fff"
                                 alt="{{ Auth::user()->full_name }}"
                                 class="h-9 w-9 rounded-full ring-2 ring-gray-200 group-hover:ring-blue-400 transition-all shadow-sm">
                        @endif
                        <span class="hidden md:inline text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">{{ Auth::user()->full_name }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-all duration-200" :class="{'rotate-180 text-blue-600': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.away="open=false" x-transition
                         class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-2xl py-2 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                            <div class="flex items-center gap-3 mb-2">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}"
                                         alt="{{ Auth::user()->full_name }}"
                                         class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=2563EB&color=fff"
                                         alt="{{ Auth::user()->full_name }}"
                                         class="h-10 w-10 rounded-full border-2 border-white shadow-sm">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->full_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('profile.show', Auth::user()) }}"
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
                            {{ __('nav.view_profile') }}
                        </a>

                        <a href="{{ route('settings.index') }}"
                           class="group flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all">
                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 text-gray-600 group-hover:text-blue-600 transition"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            {{ __('nav.settings') }}
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

            <!-- Language switcher -->
            @php($current_locale = app()->getLocale())
            @php($target_locale = $current_locale === 'vi' ? 'en' : 'vi')
            @php($flag = $target_locale === 'vi' ? '🇻🇳' : '🇬🇧')
            <a href="{{ route('locale.switch', $target_locale) }}"
               class="group px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg flex items-center gap-2 transition-all duration-200"
               title="{{ __('nav.switch_language') }}">
                <span class="hidden sm:inline">{{ $flag }} {{ strtoupper($target_locale) }}</span>
                <span class="sm:hidden">{{ $flag }}</span>
            </a>

            <!-- Support Project (only for authenticated users) -->
            @auth
                <a href="{{ route('support') }}"
                   class="group px-3 py-2 text-sm font-medium text-pink-600 hover:text-pink-700 hover:bg-pink-50 rounded-lg flex items-center gap-1 transition-all duration-200"
                   title="{{ __('nav.support_tooltip') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-all group-hover:scale-110 duration-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span class="hidden lg:inline text-xs">{{ __('nav.about') }}</span>
                </a>
            @endauth
        </div>
    </div>
</nav>

@if(request()->is('messages*'))
<main class="max-w-6xl mx-auto py-4 h-[calc(100vh-64px)] overflow-hidden">
    <div class="h-full">
        {{ $slot }}
    </div>
</main>
@else
<main class="max-w-6xl mx-auto mt-6 p-4">
    {{ $slot }}
</main>
@endif

<!-- Real-time notifications panel -->
@auth
    <livewire:notification.notifications-panel />
@endauth

<!-- Chat button - on Support and Help pages -->
@auth
    @if(request()->routeIs('support') || request()->is('help*'))
        <livewire:chat.chat-button />
        <livewire:chat.chat-panel />
    @endif
    
    {{-- Global placeholder manager - updates session even when user is on another page --}}
    <livewire:lesson.placeholder-manager />
@endauth

@livewireScripts
@vite(['resources/js/app.js'])

{{-- Tour Translations --}}
<script>
    window.tourTranslations = {
        welcome_title: @json(__('help.welcome_title')),
        welcome_text: @json(__('help.welcome_text')),
        dashboard_title: @json(__('help.dashboard_title')),
        dashboard_text: @json(__('help.dashboard_text')),
        lessons_title: @json(__('help.lessons_title')),
        lessons_text: @json(__('help.lessons_text')),
        topics_title: @json(__('help.topics_title')),
        topics_text: @json(__('help.topics_text')),
        notifications_title: @json(__('help.notifications_title')),
        notifications_text: @json(__('help.notifications_text')),
        profile_title: @json(__('help.profile_title')),
        profile_text: @json(__('help.profile_text')),
        complete_title: @json(__('help.complete_title')),
        complete_text: @json(__('help.complete_text')),
        next: @json(__('help.next')),
        back: @json(__('help.back')),
        skip: @json(__('help.skip')),
        finish: @json(__('help.finish'))
    };
</script>

@stack('scripts')

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