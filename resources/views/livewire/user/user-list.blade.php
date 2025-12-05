<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-3xl font-bold mb-2">👥 {{ __('users.title') }}</h1>
        <p class="text-blue-100">{{ __('users.subtitle') }}</p>
    </div>

    {{-- Tabs and Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Tabs --}}
            <div class="flex rounded-lg bg-gray-100 p-1">
                <button wire:click="setTab('teachers')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all {{ $tab === 'teachers' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    {{ __('users.find_teacher') }}
                </button>
                <button wire:click="setTab('students')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all {{ $tab === 'students' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ __('users.find_student') }}
                </button>
            </div>

            {{-- Language Filter --}}
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">{{ __('users.filter_by_language') }}:</label>
                <select wire:model.live="language_filter"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('users.all_languages') }}</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->value }}">{{ $lang->flag() }} {{ $lang->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Users Grid --}}
    @if($users->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($users as $user)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
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
                    <div class="px-6 py-4">
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
                        </div>

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
                            @if($tab === 'teachers' && $user->teachingLanguages->count() > 0)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('users.teaches') }}:</span>
                                    @foreach($user->teachingLanguages as $lang)
                                        {{ \App\Enums\System\SystemLanguageEnum::tryFrom($lang->language)?->flag() }}
                                        {{ \App\Enums\System\SystemLanguageEnum::tryFrom($lang->language)?->label() }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                            @endif
                            @if($tab === 'students' && $user->learning_lang)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ __('users.learning') }}:</span>
                                    {{ \App\Enums\System\SystemLanguageEnum::tryFrom($user->learning_lang)?->flag() }}
                                    {{ \App\Enums\System\SystemLanguageEnum::tryFrom($user->learning_lang)?->label() }}
                                </p>
                            @endif
                        </div>

                        {{-- Bio preview --}}
                        @if($user->bio)
                            <p class="mt-3 text-sm text-gray-500 line-clamp-2">{{ $user->bio }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center gap-2">
                        <a href="{{ route('profile.show', $user) }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('users.view_profile') }}
                        </a>
                        <button type="button"
                                class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                title="{{ __('profile.send_message') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                {{ $tab === 'teachers' ? __('users.no_teachers') : __('users.no_students') }}
            </h3>
            <p class="text-gray-500">
                {{ $tab === 'teachers' ? __('users.no_teachers_hint') : __('users.no_students_hint') }}
            </p>
        </div>
    @endif
</div>
