<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-3xl font-bold mb-2">👥 {{ __('users.title') }}</h1>
        <p class="text-blue-100">{{ __('users.subtitle') }}</p>
    </div>

    {{-- Tabs and Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Tabs for My Teachers / My Students --}}
            <div class="flex flex-wrap gap-1 rounded-lg bg-gray-100 p-1">
                <button wire:click="setTab('all')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all {{ $tab === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{ __('users.all_users') }}
                </button>
                @if(auth()->user()->isStudent())
                <button wire:click="setTab('my_teachers')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all {{ $tab === 'my_teachers' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    {{ __('users.my_teachers') }}
                </button>
                @endif
                @if(auth()->user()->isTeacher())
                <button wire:click="setTab('my_students')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all {{ $tab === 'my_students' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ __('users.my_students') }}
                </button>
                @endif
            </div>

            {{-- Filters - only for 'all' tab --}}
            @if($tab === 'all')
            <div class="flex flex-wrap items-center gap-3">
                {{-- Role Filter --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">{{ __('users.role') }}:</label>
                    <select wire:model.live="role_filter"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('users.all_roles') }}</option>
                        <option value="teacher">{{ __('profile.teacher') }}</option>
                        <option value="student">{{ __('profile.student') }}</option>
                    </select>
                </div>
                
                {{-- Language Filter --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">{{ __('users.language') }}:</label>
                    <select wire:model.live="language_filter"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('users.all_languages') }}</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->value }}">{{ $lang->flag() }} {{ $lang->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Users Grid --}}
    @if($users->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($users as $user)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col h-full">
                    {{-- Card Header with gradient --}}
                    <div class="h-16 bg-gradient-to-r from-blue-400 to-indigo-500"></div>
                    
                    {{-- Avatar --}}
                    <div class="px-6 -mt-10">
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->full_name }}"
                                 class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover">
                        @else
                            <div class="w-20 h-20 rounded-full border-4 border-white shadow-md bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-xl font-bold text-white">
                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- User Info --}}
                    <div class="px-6 py-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->full_name }}</h3>
                        
                        {{-- Roles --}}
                        <div class="flex items-center gap-2 mt-2">
                            @if($user->isTeacher())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                    {{ __('profile.teacher') }}
                                </span>
                            @endif
                            @if($user->isStudent())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                    {{ __('profile.student') }}
                                </span>
                            @endif
                            {{-- Native Language --}}
                            @if($user->native_lang)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                    {{ \App\Enums\System\SystemLanguageEnum::flagByValue($user->native_lang) }}
                                    {{ \App\Enums\System\SystemLanguageEnum::tryFrom($user->native_lang)?->label() }}
                                </span>
                            @endif
                        </div>

                        {{-- Availability status (only in all tab) --}}
                        @if($tab === 'all')
                            <div class="flex flex-wrap gap-1 mt-2">
                                @if($user->isTeacher())
                                    @if($user->is_accepting_students)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-600 text-xs rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('users.accepting_students') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('users.not_accepting_students') }}
                                        </span>
                                    @endif
                                @endif
                                @if($user->isStudent())
                                    @if($user->is_looking_for_teacher)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-600 text-xs rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('users.looking_for_teacher') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('users.not_looking_for_teacher') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        @endif

                        {{-- Rating (for teachers) --}}
                        @if($user->isTeacher())
                            <div class="flex items-center gap-2 mt-2">
                                @if($user->average_rating)
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $user->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $user->average_rating }}</span>
                                    <span class="text-xs text-gray-500">({{ $user->reviews_count }})</span>
                                @else
                                    <span class="text-xs text-gray-400">{{ __('users.no_reviews') }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Languages --}}
                        <div class="mt-3 space-y-1">
                            {{-- Teaching languages (for teachers in any tab) --}}
                            @if($user->isTeacher() && $user->teachingLanguages->count() > 0)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('users.teaches') }}:</span>
                                    @foreach($user->teachingLanguages as $lang)
                                        {{ \App\Enums\System\SystemLanguageEnum::tryFrom($lang->language)?->flag() }}
                                        {{ \App\Enums\System\SystemLanguageEnum::tryFrom($lang->language)?->label() }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                            @endif
                            {{-- Learning languages (for students in any tab) --}}
                            @if($user->isStudent() && $user->learningLanguages->count() > 0)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('users.learning') }}:</span>
                                    @foreach($user->learningLanguages as $lang)
                                        {{ \App\Enums\System\SystemLanguageEnum::tryFrom($lang->language)?->flag() }}
                                        {{ \App\Enums\System\SystemLanguageEnum::tryFrom($lang->language)?->label() }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                            @endif
                        </div>

                        {{-- Bio preview --}}
                        @if($user->bio)
                            <p class="mt-3 text-sm text-gray-500 line-clamp-2">{{ $user->bio }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center gap-2 mt-auto">
                        <a href="{{ route('profile.show', $user) }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('users.view_profile') }}
                        </a>

                        {{-- Actions tooltip for my_teachers tab --}}
                        @if($tab === 'my_teachers')
                            <div class="relative">
                                <button wire:click="toggleTooltip({{ $user->id }})"
                                        class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition {{ isset($open_tooltips[$user->id]) ? 'bg-blue-50 text-blue-600' : '' }}"
                                        title="{{ __('users.actions') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                @if(isset($open_tooltips[$user->id]))
                                    <div 
                                        x-data="{ show: true }"
                                        x-show="show"
                                        @click.outside="$wire.closeTooltip({{ $user->id }})"
                                        x-transition
                                        class="absolute right-0 bottom-full mb-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden"
                                    >
                                        <a href="{{ route('messages.chat', $user) }}"
                                           class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium">{{ __('users.send_message') }}</span>
                                        </a>

                                        <div class="border-t border-gray-100"></div>

                                        <button wire:click="confirmLeaveTeacher({{ $user->id }})"
                                                class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                            <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium">{{ __('users.leave') }}</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Actions tooltip for my_students tab --}}
                        @if($tab === 'my_students')
                            <div class="relative">
                                <button wire:click="toggleTooltip({{ $user->id }})"
                                        class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition {{ isset($open_tooltips[$user->id]) ? 'bg-blue-50 text-blue-600' : '' }}"
                                        title="{{ __('users.actions') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                @if(isset($open_tooltips[$user->id]))
                                    <div 
                                        x-data="{ show: true }"
                                        x-show="show"
                                        @click.outside="$wire.closeTooltip({{ $user->id }})"
                                        x-transition
                                        class="absolute right-0 bottom-full mb-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden"
                                    >
                                        <a href="{{ route('messages.chat', $user) }}"
                                           class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium">{{ __('users.send_message') }}</span>
                                        </a>

                                        <div class="border-t border-gray-100"></div>

                                        <button wire:click="confirmRemoveStudent({{ $user->id }})"
                                                class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                            <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium">{{ __('users.remove') }}</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        {{-- Actions for all tab --}}
                        @if($tab === 'all')
                            <div class="relative">
                                <button wire:click="toggleTooltip({{ $user->id }})"
                                        class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition {{ isset($open_tooltips[$user->id]) ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                @if(isset($open_tooltips[$user->id]))
                                    <div 
                                        x-data="{ show: true }"
                                        x-show="show"
                                        @click.outside="$wire.closeTooltip({{ $user->id }})"
                                        x-transition
                                        class="absolute right-0 bottom-full mb-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden"
                                    >
                                        <a href="{{ route('messages.chat', $user) }}"
                                           class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                            <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium">{{ __('users.send_message') }}</span>
                                        </a>

                                        @if(auth()->user()->isTeacher() && $user->isStudent() && $user->is_looking_for_teacher)
                                            <div class="border-t border-gray-100"></div>
                                            <button wire:click="inviteStudent({{ $user->id }})"
                                                    class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-green-600 hover:bg-green-50 transition">
                                                <div class="p-1.5 rounded-lg bg-green-50 group-hover:bg-green-100 transition-colors">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                                    </svg>
                                                </div>
                                                <span class="font-medium">{{ __('users.invite_student') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination (only for paginated results) --}}
        @if($users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $users->links() }}
        </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                @if($tab === 'my_teachers')
                    {{ __('users.no_my_teachers') }}
                @elseif($tab === 'my_students')
                    {{ __('users.no_my_students') }}
                @else
                    {{ __('users.no_users_found') }}
                @endif
            </h3>
            <p class="text-gray-500">
                @if($tab === 'my_teachers')
                    {{ __('users.no_my_teachers_hint') }}
                @elseif($tab === 'my_students')
                    {{ __('users.no_my_students_hint') }}
                @else
                    {{ __('users.no_users_found_hint') }}
                @endif
            </p>
        </div>
    @endif

    {{-- Confirm Remove Modal --}}
    @if($show_confirm_modal)
        <x-confirm-modal-livewire
            icon="warning"
            :title="$remove_action === 'leave_teacher' ? __('users.leave_teacher_title') : __('users.remove_student_title')"
            :message="$remove_action === 'leave_teacher' ? __('users.confirm_leave_teacher') : __('users.confirm_remove_student')"
            :confirmText="$remove_action === 'leave_teacher' ? __('users.leave') : __('users.remove')"
            :cancelText="__('lessons.cancel')"
            confirmMethod="executeRemove"
            cancelMethod="closeConfirmModal"
        />
    @endif
</div>
