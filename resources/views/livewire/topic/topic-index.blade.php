<div class="max-w-5xl mx-auto p-6">
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">{{ __('topics.manage_topics') }}</h2>
            </div>

            {{-- Filter button --}}
            @if($topics->count() > 0 || $this->hasActiveFilters())
                <button
                    wire:click="toggleFilters"
                    class="group flex items-center gap-2 px-4 py-2 {{ $show_filters ? 'bg-blue-600 text-white' : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 hover:from-gray-200 hover:to-gray-300' }} text-sm font-medium rounded-lg shadow transition"
                    title="{{ __('topics.filters') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('topics.filters') }}</span>
                    @if($search || !empty($statuses) || $sort !== 'newest')
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-white text-blue-600 rounded-full">!</span>
                    @endif
                </button>
            @endif
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm flex items-center gap-3">
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
        @endif

        {{-- Filters Panel --}}
        @if($show_filters)
            <div class="mb-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4" wire:key="filters-panel">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Search --}}
                    <div>
                        <label for="temp_search" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('topics.search') }}
                        </label>
                        <input
                            type="text"
                            id="temp_search"
                            wire:model.live="temp_search"
                            placeholder="{{ __('topics.search_placeholder') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        />
                    </div>

                    {{-- Status Filter --}}
                    <div class="relative" x-data="{ open: @entangle('show_status_dropdown') }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('topics.status') }}
                            @if(!empty($temp_statuses))
                                <span class="text-xs text-blue-600 font-semibold">({{ count($temp_statuses) }})</span>
                            @endif
                        </label>
                        
                        {{-- Dropdown Button --}}
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-left flex items-center justify-between"
                        >
                            <span class="text-sm text-gray-700">
                                @if(empty($temp_statuses))
                                    {{ __('topics.all_statuses') }}
                                @else
                                    {{ implode(', ', array_map(fn($s) => $s === 'system' ? __('topics.system') : __('topics.custom'), $temp_statuses)) }}
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
                                @foreach($this->getStatusOptions() as $option)
                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input
                                            type="checkbox"
                                            wire:model.live="temp_statuses"
                                            value="{{ $option['value'] }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                        />
                                        <span class="text-sm text-gray-700 font-medium">{{ $option['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label for="temp_sort" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('topics.sort_by') }}
                        </label>
                        <select
                            id="temp_sort"
                            wire:model.live="temp_sort"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                            <option value="newest">{{ __('topics.newest_first') }}</option>
                            <option value="oldest">{{ __('topics.oldest_first') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <button
                        wire:click="resetFilters"
                        class="text-sm text-gray-600 hover:text-gray-800 font-medium transition"
                    >
                        {{ __('topics.reset_filters') }}
                    </button>

                    <button
                        wire:click="applyFilters"
                        class="group flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('topics.apply_filters_with_count', ['count' => $this->filteredTopicsCount]) }}
                    </button>
                </div>
            </div>
        @endif

        <div class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <h3 class="text-lg font-semibold text-blue-900">{{ __('topics.create_new_topic') }}</h3>
            </div>
            @livewire('onboarding.create-topic')
        </div>

        <div class="border-t border-gray-200 pt-8">
            <div class="flex items-center gap-2 mb-6">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <h3 class="text-xl font-bold text-gray-900">{{ __('topics.your_topics') }}</h3>
                <p class="text-sm text-gray-500 ml-auto">{{ __('topics.visibility_description') }}</p>
            </div>

            @if($topics->count() > 0)
                @php
                    // Separate system and user topics
                    $system_topics = $topics->filter(fn($t) => $t->user_id === null);
                    $user_topics = $topics->filter(fn($t) => $t->user_id !== null);
                @endphp

                {{-- System Topics Section --}}
                @if($system_topics->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h4 class="text-lg font-bold text-gray-900">{{ __('topics.system_topics') }}</h4>
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full font-medium">{{ $system_topics->count() }}</span>
                        </div>
                        <div class="space-y-4">
                            @foreach($system_topics as $topic)
                                @php
                                    $locale = app()->getLocale();
                                    $is_visible = in_array($topic->id, $visible_topic_ids, true);
                                    $is_expanded = in_array($topic->id, $expanded_topics, true);
                                    $keyword_count = $topic->keywords->count();
                                @endphp
                                @include('livewire.topic.partials.topic-item')
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- User Topics Section --}}
                @if($user_topics->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h4 class="text-lg font-bold text-gray-900">{{ __('topics.user_topics') }}</h4>
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded-full font-medium">{{ $user_topics->count() }}</span>
                        </div>
                        <div class="space-y-4">
                            @foreach($user_topics as $topic)
                                @php
                                    $locale = app()->getLocale();
                                    $is_visible = in_array($topic->id, $visible_topic_ids, true);
                                    $is_expanded = in_array($topic->id, $expanded_topics, true);
                                    $keyword_count = $topic->keywords->count();
                                @endphp
                                @include('livewire.topic.partials.topic-item')
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $topics->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    @if($this->hasActiveFilters())
                        {{-- No results from filters --}}
                        <svg class="mx-auto h-14 w-14 text-gray-400 drop-shadow-md mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-900">{{ __('topics.no_results_found') }}</h3>
                        <p class="text-sm text-gray-500 mt-2">{{ __('topics.no_results_message') }}</p>
                        <button wire:click="resetFilters"
                                class="mt-4 inline-flex items-center gap-2 px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <svg class="h-5 w-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('topics.reset_filters') }}
                        </button>
                    @else
                        {{-- No topics at all --}}
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-gray-700">{{ __('topics.no_topics_yet') }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ __('topics.create_first_topic') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
