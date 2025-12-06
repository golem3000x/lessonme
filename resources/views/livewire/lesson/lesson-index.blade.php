@php use Illuminate\Support\Str; @endphp
<div class="max-w-6xl mx-auto p-6 space-y-8">
    {{-- Header with Tabs --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600 drop-shadow-md" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ __('lessons.lessons') }}
            </h2>
        </div>

        {{-- View Mode Tabs --}}
        <div class="flex items-center justify-between">
            <div class="flex gap-1 p-1 bg-gray-100 rounded-lg">
                <button
                    wire:click="setViewMode('my_lessons')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $view_mode === 'my_lessons' ? 'bg-white text-blue-600 shadow' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    {{ __('lessons.my_lessons') }}
                </button>
                <button
                    wire:click="setViewMode('assigned')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $view_mode === 'assigned' ? 'bg-white text-blue-600 shadow' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    {{ __('lessons.assigned_to_me') }}
                </button>
            </div>

        <div class="flex items-center gap-2">
            {{-- Show filter and sort buttons if there are lessons OR if filters are active OR if placeholders exist --}}
            @if(!$lessons->isEmpty() || $this->hasActiveFilters() || !empty($creating_lessons_placeholders) || !empty($regenerating_lessons_placeholders))
                {{-- Sort dropdown --}}
                <div class="relative" x-data="{ open: @entangle('show_sort_dropdown') }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="group flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm font-medium rounded-lg shadow transition"
                        title="{{ __('lessons.sort_by') }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        <span class="hidden sm:inline">{{ __('lessons.sort') }}</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 right-0 mt-1 w-48 bg-white border border-gray-300 rounded-lg shadow-lg"
                        style="display: none;"
                    >
                        <div class="p-2">
                            <button
                                wire:click="setSort('newest')"
                                @click="open = false"
                                class="w-full text-left px-3 py-2 text-sm rounded hover:bg-gray-50 transition {{ $sort === 'newest' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}"
                            >
                                {{ __('lessons.newest_first') }}
                            </button>
                            <button
                                wire:click="setSort('oldest')"
                                @click="open = false"
                                class="w-full text-left px-3 py-2 text-sm rounded hover:bg-gray-50 transition {{ $sort === 'oldest' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}"
                            >
                                {{ __('lessons.oldest_first') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filter button --}}
                <button
                        wire:click="toggleFilters"
                        class="group flex items-center gap-2 px-4 py-2 {{ $show_filters ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} text-sm font-medium rounded-lg shadow transition"
                        title="{{ __('lessons.filters') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('lessons.filters') }}</span>
                    @if($search || !empty($language_levels) || !empty($lesson_languages))
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-white text-blue-600 rounded-full">!</span>
                    @endif
                </button>
            @endif

            {{-- Create button - only show in my_lessons view --}}
            @if($view_mode === 'my_lessons' && (!$lessons->isEmpty() || !$this->hasActiveFilters() || !empty($creating_lessons_placeholders) || !empty($regenerating_lessons_placeholders)))
                <button
                        wire:click="openManageModal"
                        class="group flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('lessons.create_new_lesson') }}</span>
                </button>
            @endif
        </div>
        </div>
    </div>

    {{-- Filters Panel --}}
    @if($show_filters)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4" wire:key="filters-panel">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Search --}}
                <div>
                    <label for="temp_search" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.search') }}
                    </label>
                    <input
                            type="text"
                            id="temp_search"
                            wire:model.live.debounce.300ms="temp_search"
                            placeholder="{{ __('lessons.search_placeholder') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    />
                </div>

                {{-- Language Level --}}
                <div class="relative" x-data="{ open: @entangle('show_level_dropdown') }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.level') }}
                        @if(!empty($temp_language_levels))
                            <span class="text-xs text-blue-600 font-semibold">({{ count($temp_language_levels) }})</span>
                        @endif
                    </label>
                    
                    {{-- Dropdown Button --}}
                    <button
                            type="button"
                            @click="open = !open"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-left flex items-center justify-between"
                    >
                        <span class="text-sm text-gray-700">
                            @if(empty($temp_language_levels))
                                {{ __('lessons.all_levels') }}
                            @else
                                {{ implode(', ', $temp_language_levels) }}
                            @endif
                        </span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                            style="display: none;"
                    >
                        <div class="p-2 space-y-1">
                            @foreach($this->getLanguageLevels() as $level)
                                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                    <input
                                            type="checkbox"
                                            wire:model.live="temp_language_levels"
                                            value="{{ $level['value'] }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    />
                                    <span class="text-sm text-gray-700 font-medium">{{ $level['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Lesson Language --}}
                <div class="relative" x-data="{ open: @entangle('show_language_dropdown') }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.lesson_language') }}
                        @if(!empty($temp_lesson_languages))
                            <span class="text-xs text-blue-600 font-semibold">({{ count($temp_lesson_languages) }})</span>
                        @endif
                    </label>
                    
                    {{-- Dropdown Button --}}
                    <button
                            type="button"
                            @click="open = !open"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-left flex items-center justify-between"
                    >
                        <span class="text-sm text-gray-700 flex items-center gap-2">
                            @if(empty($temp_lesson_languages))
                                {{ __('lessons.all_languages') }}
                            @else
                                @foreach($this->getLessonLanguages() as $lang)
                                    @if(in_array($lang['value'], $temp_lesson_languages))
                                        <span class="inline-flex items-center gap-1">
                                            {{ $lang['flag'] }}
                                            <span class="hidden sm:inline">{{ $lang['label'] }}</span>
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        </span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                            style="display: none;"
                    >
                        <div class="p-2 space-y-1">
                            @foreach($this->getLessonLanguages() as $language)
                                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                    <input
                                            type="checkbox"
                                            wire:model.live="temp_lesson_languages"
                                            value="{{ $language['value'] }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    />
                                    <span class="text-xl">{{ $language['flag'] }}</span>
                                    <span class="text-sm text-gray-700 font-medium">{{ $language['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <button
                        wire:click="resetFilters"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('lessons.reset') }}
                </button>

                <button
                        wire:click="applyFilters"
                        class="group flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('lessons.apply_filters_with_count', ['count' => $this->filteredLessonsCount]) }}
                </button>
            </div>
        </div>
    @endif

    {{-- Flash messages --}}
    @if (session()->has('message'))
        <div class="flex items-center gap-2 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg">
            <svg class="w-5 h-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="flex items-center gap-2 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg">
            <svg class="w-5 h-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Empty list --}}
    @if($lessons->isEmpty() && empty($creating_lessons_placeholders) && empty($regenerating_lessons_placeholders))
        <div class="text-center py-16 space-y-4">
            @if($this->hasActiveFilters())
                {{-- No results from filters --}}
                <svg class="mx-auto h-14 w-14 text-gray-400 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_results_found') }}</h3>
                <p class="text-sm text-gray-500">{{ __('lessons.no_results_message') }}</p>
                <button wire:click="resetFilters"
                        class="mt-4 inline-flex items-center gap-2 px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <svg class="h-5 w-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('lessons.reset_filters') }}
                </button>
            @else
                {{-- No lessons at all --}}
                <svg class="mx-auto h-14 w-14 text-gray-400 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                @if($view_mode === 'assigned')
                    <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_assigned_lessons') }}</h3>
                @else
                    <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_lessons_yet') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('lessons.get_started_message') }}</p>
                @endif
            @endif
        </div>
    @endif

    {{-- Lessons list (show if there are lessons OR placeholders) --}}
    @if(!$lessons->isEmpty() || ($view_mode === 'my_lessons' && (!empty($creating_lessons_placeholders) || !empty($regenerating_lessons_placeholders))))
        {{-- Lessons list --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            {{-- Placeholder cards for NEW lessons being created (only in my_lessons view) --}}
            @if($view_mode === 'my_lessons')
            @foreach($creating_lessons_placeholders as $placeholder_id)
                <div wire:key="placeholder-new-{{ $placeholder_id }}" class="group bg-white border border-gray-100 rounded-xl shadow-sm p-5 relative overflow-hidden animate-pulse">
                    {{-- Animated gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 opacity-50"></div>
                    
                    {{-- Pulsing overlay animation --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent animate-shimmer"></div>
                    
                    <div class="relative">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 space-y-3">
                                {{-- Title skeleton --}}
                                <div class="h-5 bg-gray-300 rounded w-3/4 animate-pulse"></div>
                                <div class="h-4 bg-gray-300 rounded w-1/2 animate-pulse"></div>
                                
                                {{-- Badge and time skeleton --}}
                                <div class="flex gap-2 mt-3">
                                    <div class="h-5 w-16 bg-blue-200 rounded-full animate-pulse"></div>
                                    <div class="h-5 w-20 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Description skeleton --}}
                        <div class="space-y-2 mb-4">
                            <div class="h-3 bg-gray-200 rounded w-full animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6 animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/6 animate-pulse"></div>
                        </div>

                        {{-- Creating message --}}
                        <div class="flex items-center justify-center w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-md font-medium">
                            <span class="text-sm">{{ __('lessons.creating_lesson') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Placeholder cards for lessons being REGENERATED --}}
            @foreach($regenerating_lessons_placeholders as $lesson_id => $placeholder_data)
                <div wire:key="placeholder-regen-{{ $placeholder_data['id'] }}" class="group bg-white border border-gray-100 rounded-xl shadow-sm p-5 relative overflow-hidden animate-pulse">
                    {{-- Animated gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 opacity-50"></div>
                    
                    {{-- Pulsing overlay animation --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent animate-shimmer"></div>
                    
                    <div class="relative">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 space-y-3">
                                {{-- Title skeleton --}}
                                <div class="h-5 bg-gray-300 rounded w-3/4 animate-pulse"></div>
                                <div class="h-4 bg-gray-300 rounded w-1/2 animate-pulse"></div>
                                
                                {{-- Badge and time skeleton --}}
                                <div class="flex gap-2 mt-3">
                                    <div class="h-5 w-16 bg-blue-200 rounded-full animate-pulse"></div>
                                    <div class="h-5 w-20 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Description skeleton --}}
                        <div class="space-y-2 mb-4">
                            <div class="h-3 bg-gray-200 rounded w-full animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6 animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/6 animate-pulse"></div>
                        </div>

                        {{-- Regenerating message --}}
                        <div class="flex items-center justify-center w-full px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-md font-medium">
                            <span class="text-sm">{{ __('lessons.lesson_regeneration_in_progress') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
            @endif

            {{-- Real lesson cards --}}
            @foreach($lessons as $lesson)
                {{-- Skip lesson if it's being regenerated (placeholder will be shown instead) --}}
                @if(isset($regenerating_lessons_placeholders[$lesson->id]))
                    @continue
                @endif
                
                <div class="group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:-translate-y-1 transition p-5 relative overflow-hidden flex flex-col h-full">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/20 to-transparent opacity-0 group-hover:opacity-100 transition pointer-events-none"></div>

                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-800 line-clamp-2 mb-2 min-h-[3.5rem]">{{ $lesson->lesson_name }}</h3>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                @if($lesson->lesson_lang)
                                    <span class="text-xl" title="{{ $lesson->lesson_lang->label() }}">
                                        {{ $lesson->lesson_lang->flag() }}
                                    </span>
                                @endif
                                
                                @if($lesson->language_level)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lesson->level_badge_color }}">
                                        {{ $lesson->language_level->value }}
                                    </span>
                                @endif
                                
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 drop-shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $lesson->formatted_created_at }}
                                </span>
                            </div>
                        </div>

                        {{-- Dropdown menu - only in my_lessons view --}}
                        @if($view_mode === 'my_lessons')
                        <div class="relative ml-2">
                            <!-- Three dots button -->
                            <button wire:click="toggleDropdown({{ $lesson->id }})"
                                    class="group p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 {{ !empty($open_dropdowns[$lesson->id]) ? 'bg-blue-50 text-blue-600' : '' }}"
                                    title="{{ __('lessons.actions') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            @if(!empty($open_dropdowns[$lesson->id]))
                            <div 
                                x-data="{ show: true }"
                                x-show="show"
                                @click.outside="$wire.closeDropdown({{ $lesson->id }})"
                                x-transition
                                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden"
                            >
                                <button wire:click="confirmRegenerate({{ $lesson->id }})"
                                        class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-yellow-600 hover:bg-yellow-50 transition-all duration-200 whitespace-nowrap">
                                    <div class="p-1.5 rounded-lg bg-yellow-50 group-hover:bg-yellow-100 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ __('lessons.regenerate_lesson') }}</span>
                                </button>

                                @if(auth()->user()->isTeacher())
                                <div class="border-t border-gray-100"></div>

                                <button wire:click="openAssignModal({{ $lesson->id }})"
                                        class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-green-600 hover:bg-green-50 transition-all duration-200 whitespace-nowrap">
                                    <div class="p-1.5 rounded-lg bg-green-50 group-hover:bg-green-100 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ __('lessons.assign_to') }}</span>
                                </button>
                                @endif

                                <div class="border-t border-gray-100"></div>

                                <a href="{{ route('lessons.history', $lesson->uuid) }}"
                                   class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                                    <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                        <svg class="h-4 w-4 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ __('lessons.history') }}</span>
                                </a>

                                <div class="border-t border-gray-100"></div>

                                <button wire:click="confirmDelete({{ $lesson->id }})"
                                        class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-200">
                                    <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ __('lessons.delete') }}</span>
                                </button>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mb-4 leading-snug line-clamp-3 flex-grow">{{ $lesson->lesson_text }}</p>

                    <a href="{{ route('lessons.show', $lesson->uuid) }}"
                       class="group inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition mt-auto">
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M14 5l7 7-7 7M3 12h18"/>
                        </svg>
                        {{ __('lessons.view_lesson') }}
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Pagination (only if there are real lessons) --}}
        @if(!$lessons->isEmpty())
            <div class="mt-8">
                {{ $lessons->links() }}
            </div>
        @endif
    @endif

    {{-- Modal --}}
    @if($show_manage_modal)
        <div
                x-data="{ open: true }"
                x-show="open"
                x-init="document.body.style.overflow = 'hidden'"
                x-on:remove="document.body.style.overflow = 'auto'"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="$wire.closeManageModal()"
        >
            {{-- Background with blur --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                 x-show="open"
                 x-transition
            ></div>

            {{-- Center modal window --}}
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                        class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden border border-gray-100"
                        x-show="open"
                        x-transition
                        @click.stop
                >
                    <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ __('lessons.manage_lesson') }}</h3>
                        </div>
                        <button wire:click="closeManageModal" class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 max-h-[calc(90vh-80px)] overflow-y-auto">
                        <livewire:lesson.manage-lesson :key="'manage-lesson-'.now()->timestamp"/>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($show_delete_confirm)
        <x-confirm-modal-livewire
            icon="danger"
            :title="__('lessons.delete_lesson_title')"
            :message="__('lessons.delete_lesson_message')"
            :confirmText="__('lessons.delete')"
            :cancelText="__('lessons.cancel')"
            confirmMethod="deleteLesson"
            cancelMethod="cancelDelete"
        />
    @endif

    {{-- Regenerate Confirmation Modal --}}
    @if($show_regenerate_confirm)
        <x-confirm-modal-livewire
            icon="warning"
            :title="__('lessons.regenerate_lesson_title')"
            :message="__('lessons.regenerate_lesson_message')"
            :confirmText="__('lessons.regenerate')"
            :cancelText="__('lessons.cancel')"
            confirmMethod="regenerateLesson"
            cancelMethod="cancelRegenerate"
        />
    @endif

    {{-- Assign Lesson Modal --}}
    @if($show_assign_modal)
        <div
            x-data="{ open: true }"
            x-show="open"
            x-init="document.body.style.overflow = 'hidden'"
            x-on:remove="document.body.style.overflow = 'auto'"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="$wire.closeAssignModal()"
        >
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" x-show="open" x-transition></div>

            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[80vh] overflow-hidden"
                    x-show="open"
                    x-transition
                    @click.stop
                >
                    {{-- Header --}}
                    <div class="p-5 bg-gradient-to-r from-green-600 to-emerald-600 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">{{ __('lessons.assign_to_students') }}</h3>
                        </div>
                        <button wire:click="closeAssignModal" class="p-1.5 rounded-lg bg-white/20 hover:bg-white/30 transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 overflow-y-auto max-h-[50vh]">
                        @if($this->myStudents->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="mt-2 text-gray-600">{{ __('lessons.no_students_yet') }}</p>
                                <a href="{{ route('users.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">
                                    {{ __('lessons.add_students_hint') }}
                                </a>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($this->myStudents as $student)
                                    @php
                                        $is_assigned = in_array($student->id, $this->assignedStudentIds);
                                    @endphp

                                    <div class="flex items-center gap-3 p-3 rounded-lg border {{ $is_assigned ? 'border-emerald-300 bg-emerald-50' : 'border-gray-200' }}">
                                        <div class="flex-shrink-0">
                                            @if($student->avatar)
                                                <img src="{{ $student->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $student->full_name }}</p>
                                            <p class="text-sm text-gray-500 truncate">{{ $student->email }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($is_assigned)
                                                <button
                                                    wire:click="unassignStudent({{ $student->id }})"
                                                    class="px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition"
                                                >
                                                    {{ __('lessons.unassign') }}
                                                </button>
                                            @else
                                                <button
                                                    wire:click="assignSingleStudent({{ $student->id }})"
                                                    class="px-3 py-1.5 text-sm font-medium text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition"
                                                >
                                                    {{ __('lessons.assign') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('close-manage-modal', () => $wire.closeManageModal());
</script>
@endscript