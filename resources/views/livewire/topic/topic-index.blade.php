<div class="space-y-6" 
    @php
        $has_unapproved_topics = $active_tab === 'custom' && $custom_topics->contains(fn($t) => !$t->is_approved);
        $has_unapproved_keywords = $active_tab === 'custom' && $custom_topics->contains(function($t) {
            return $t->keywords->contains(fn($k) => !$k->is_approved);
        });
    @endphp
    @if($has_unapproved_topics || $has_unapproved_keywords)
        wire:poll.5s
    @endif
>
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

            {{-- Filter and Sort buttons - only for custom topics tab --}}
            @if($active_tab === 'custom' && ($custom_topics->count() > 0 || $this->hasActiveFilters()))
                <div class="flex items-center gap-2">
                    {{-- Sort dropdown --}}
                    <div class="relative" x-data="{ open: @entangle('show_sort_dropdown') }">
                        <button
                            type="button"
                            @click="open = !open"
                            class="group flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 hover:from-gray-200 hover:to-gray-300 text-sm font-medium rounded-lg shadow transition"
                            title="{{ __('topics.sort_by') }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('topics.sort') }}</span>
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
                                    {{ __('topics.newest_first') }}
                                </button>
                                <button
                                    wire:click="setSort('oldest')"
                                    @click="open = false"
                                    class="w-full text-left px-3 py-2 text-sm rounded hover:bg-gray-50 transition {{ $sort === 'oldest' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}"
                                >
                                    {{ __('topics.oldest_first') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Filter button --}}
                    <button
                        wire:click="toggleFilters"
                        class="group flex items-center gap-2 px-4 py-2 {{ $show_filters ? 'bg-blue-600 text-white' : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 hover:from-gray-200 hover:to-gray-300' }} text-sm font-medium rounded-lg shadow transition"
                        title="{{ __('topics.filters') }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span class="hidden sm:inline">{{ __('topics.filters') }}</span>
                        @if($search)
                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-white text-blue-600 rounded-full">!</span>
                        @endif
                    </button>
                </div>
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

        {{-- Filters Panel - only for custom topics --}}
        <div 
            class="mb-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4 {{ !($show_filters && $active_tab === 'custom') ? 'hidden' : '' }}" 
            wire:key="filters-panel"
        >
            {{-- Search --}}
            <div>
                <label for="temp_search" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('topics.search') }}
                </label>
                <input
                    type="text"
                    id="temp_search"
                    wire:model.live.debounce.300ms="temp_search"
                    placeholder="{{ __('topics.search_placeholder') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                />
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
                    {{ __('topics.reset') }}
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

        {{-- Create Topic Section - only for custom tab --}}
        @if($active_tab === 'custom')
            <div class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">{{ __('topics.create_new_topic') }}</h3>
                </div>
                @livewire('slug.create-topic')
            </div>
        @endif

        {{-- Tabs --}}
        <div class="mb-6">
            <div class="border-b border-gray-200 overflow-x-auto">
                <nav class="flex gap-1 sm:gap-4 min-w-max">
                    <button
                        wire:click="switchTab('custom')"
                        class="relative px-2 sm:px-4 py-3 font-medium text-sm transition-all whitespace-nowrap {{ $active_tab === 'custom' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        <span class="flex items-center gap-1 sm:gap-2">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('topics.user_topics') }}</span>
                            <span class="sm:hidden">{{ __('topics.your_short') }}</span>
                            @if($active_tab === 'custom')
                                <span class="inline-flex items-center justify-center px-1.5 sm:px-2 py-0.5 text-xs font-bold bg-blue-100 text-blue-600 rounded-full">{{ $custom_topics->total() }}</span>
                            @endif
                        </span>
                    </button>

                    <button
                        wire:click="switchTab('system')"
                        class="relative px-2 sm:px-4 py-3 font-medium text-sm transition-all whitespace-nowrap {{ $active_tab === 'system' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        <span class="flex items-center gap-1 sm:gap-2">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('topics.system_topics') }}</span>
                            <span class="sm:hidden">{{ __('topics.system_short') }}</span>
                            @if($active_tab === 'system')
                                <span class="inline-flex items-center justify-center px-1.5 sm:px-2 py-0.5 text-xs font-bold bg-gray-100 text-gray-600 rounded-full">{{ $system_topics->count() }}</span>
                            @endif
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-4">
            @if($active_tab === 'system')
                {{-- System Topics --}}
                @if($system_topics->count() > 0)
                    @foreach($system_topics as $topic)
                        <div wire:key="topic-container-{{ $topic->id }}">
                            @php
                                $locale = app()->getLocale();
                                $is_visible = in_array($topic->id, $visible_topic_ids, true);
                                $is_expanded = in_array($topic->id, $expanded_topics, true);
                                $keyword_count = $topic->keywords->count();
                            @endphp
                            @include('livewire.topic.partials.topic-item')
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-gray-700">{{ __('topics.no_system_topics') }}</p>
                    </div>
                @endif
            @else
                {{-- Custom Topics --}}
                {{-- Placeholder items for topics being created --}}
                @foreach($creating_topics_placeholders as $placeholder_id)
                    <div wire:key="placeholder-{{ $placeholder_id }}" class="group relative bg-white border-2 border-blue-200 rounded-xl shadow-md overflow-hidden animate-pulse">
                        {{-- Animated gradient background --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 opacity-50"></div>
                        
                        {{-- Pulsing overlay animation --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent animate-shimmer"></div>
                        
                        {{-- Header --}}
                        <div class="flex items-center gap-4 p-5 bg-gradient-to-r from-gray-50 to-white relative">
                            {{-- Visibility toggle skeleton --}}
                            <div class="w-10 h-10 bg-gray-200 rounded-lg animate-pulse"></div>

                            {{-- Topic name skeleton --}}
                            <div class="flex-1">
                                <div class="h-5 bg-gray-300 rounded w-1/3 mb-2 animate-pulse"></div>
                                <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                            </div>

                            {{-- Badge skeleton --}}
                            <div class="h-6 w-16 bg-blue-200 rounded-full animate-pulse"></div>

                            {{-- Loading spinner --}}
                            <div class="ml-2">
                                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Creating message --}}
                        <div class="p-4 bg-white/50 relative">
                            <div class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg font-medium">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm">{{ __('topics.creating_topic') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Real topic items --}}
                @if($custom_topics->count() > 0)
                    @foreach($custom_topics as $topic)
                        <div wire:key="topic-container-{{ $topic->id }}">
                            @php
                                $locale = app()->getLocale();
                                $is_visible = in_array($topic->id, $visible_topic_ids, true);
                                $is_expanded = in_array($topic->id, $expanded_topics, true);
                                $keyword_count = $topic->keywords->count();
                            @endphp
                            @include('livewire.topic.partials.topic-item')
                        </div>
                    @endforeach

                    {{-- Pagination - only for custom topics --}}
                    <div class="mt-8">
                        {{ $custom_topics->links() }}
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
            @endif
        </div>
    </div>

    {{-- Delete Topic Confirmation Modal --}}
    @if($show_delete_topic_confirm)
        <x-confirm-modal-livewire
            icon="danger"
            :title="__('topics.delete_topic_title')"
            :message="__('topics.delete_topic_message')"
            :confirmText="__('topics.delete')"
            :cancelText="__('topics.cancel')"
            confirmMethod="confirmDeleteTopic"
            cancelMethod="cancelDeleteTopic"
        />
    @endif

    {{-- Delete Keyword Confirmation Modal --}}
    @if($show_delete_keyword_confirm)
        <x-confirm-modal-livewire
            icon="danger"
            :title="__('topics.delete_keyword_title')"
            :message="__('topics.delete_keyword_message')"
            :confirmText="__('topics.delete')"
            :cancelText="__('topics.cancel')"
            confirmMethod="confirmDeleteKeyword"
            cancelMethod="cancelDeleteKeyword"
        />
    @endif
</div>
