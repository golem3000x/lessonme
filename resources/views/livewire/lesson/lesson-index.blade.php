@php use Illuminate\Support\Str; @endphp
<div class="max-w-6xl mx-auto p-6 space-y-8">
    {{-- Заголовок --}}
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600 drop-shadow-md" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            {{ __('lessons.my_lessons') }}
        </h2>

        <div class="flex items-center gap-2">
            {{-- Show filter button if there are lessons OR if filters are active --}}
            @if(!$lessons->isEmpty() || $this->hasActiveFilters())
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
                    @if($search || !empty($language_levels) || $sort !== 'newest')
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-white text-blue-600 rounded-full">!</span>
                    @endif
                </button>
            @endif

            {{-- Create button always visible if lessons exist or no filters applied --}}
            @if(!$lessons->isEmpty() || !$this->hasActiveFilters())
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
                            wire:model.live="temp_search"
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

                {{-- Sort --}}
                <div>
                    <label for="temp_sort" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.sort_by') }}
                    </label>
                    <select
                            id="temp_sort"
                            wire:model.live="temp_sort"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    >
                        <option value="newest">{{ __('lessons.newest_first') }}</option>
                        <option value="oldest">{{ __('lessons.oldest_first') }}</option>
                    </select>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <button
                        wire:click="resetFilters"
                        class="text-sm text-gray-600 hover:text-gray-800 font-medium transition"
                >
                    {{ __('lessons.reset_filters') }}
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

    {{-- Пустой список --}}
    @if($lessons->isEmpty())
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
                <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_lessons_yet') }}</h3>
                <p class="text-sm text-gray-500">{{ __('lessons.get_started_message') }}</p>
            @endif
        </div>
    @else
        {{-- Список уроков --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Placeholder cards for lessons being created --}}
            @foreach($creating_lessons_placeholders as $placeholder_id)
                <div wire:key="placeholder-{{ $placeholder_id }}" class="group bg-white border border-gray-100 rounded-xl shadow-sm p-5 relative overflow-hidden animate-pulse">
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

                            {{-- Loading spinner --}}
                            <div class="ml-2">
                                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Description skeleton --}}
                        <div class="space-y-2 mb-4">
                            <div class="h-3 bg-gray-200 rounded w-full animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6 animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/6 animate-pulse"></div>
                        </div>

                        {{-- Creating message --}}
                        <div class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-md font-medium">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">{{ __('lessons.creating_lesson') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{-- Real lesson cards --}}
            @foreach($lessons as $lesson)
                <div class="group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:-translate-y-1 transition p-5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/20 to-transparent opacity-0 group-hover:opacity-100 transition pointer-events-none"></div>

                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-800 line-clamp-2 mb-2">{{ $lesson->lesson_name }}</h3>
                            
                            <div class="flex flex-wrap items-center gap-2">
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

                        <div class="relative ml-2">
                            <!-- Кнопка с тремя точками -->
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

                                <div class="border-t border-gray-100"></div>

                                <a href="{{ route('lessons.history', $lesson->id) }}"
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
                    </div>

                    <p class="text-sm text-gray-600 mb-4 leading-snug">{{ Str::limit($lesson->lesson_text, 120) }}</p>

                    <a href="{{ route('lessons.show', $lesson->id) }}"
                       class="group inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M14 5l7 7-7 7M3 12h18"/>
                        </svg>
                        {{ __('lessons.view_lesson') }}
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $lessons->links() }}
        </div>
    @endif

    {{-- Modal --}}
    @if($show_manage_modal)
        <div
                x-data="{ open: true }"
                x-show="open"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="$wire.closeManageModal()"
        >
            {{-- Фон с размытием --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                 x-show="open"
                 x-transition
            ></div>

            {{-- Центрируем модальное окно --}}
            <div class="flex items-center justify-center min-h-screen p-4">
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
</div>

@script
<script>
    $wire.on('close-manage-modal', () => $wire.closeManageModal());
</script>
@endscript