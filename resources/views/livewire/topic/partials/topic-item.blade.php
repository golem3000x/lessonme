<div class="group relative bg-white border-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 {{ $is_visible ? 'border-green-200' : 'border-gray-200' }} overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center gap-4 p-5 bg-gradient-to-r from-gray-50 to-white">
        {{-- Visibility toggle --}}
        <button 
            wire:click="toggleTopicVisibility({{ $topic->id }})"
            class="group/eye p-2 rounded-lg transition-all duration-200 {{ $is_visible ? 'bg-green-100 hover:bg-green-200' : 'bg-gray-100 hover:bg-gray-200' }}"
            title="{{ $is_visible ? __('topics.visible_in_chat') : __('topics.hidden_in_chat') }}"
        >
            @if($is_visible)
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            @else
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            @endif
        </button>

        {{-- Topic name + expand button --}}
        <button
            wire:click="toggleAccordion({{ $topic->id }})"
            class="flex-1 flex items-center gap-3 text-left"
        >
            @php
                $title = $topic->title($locale);
                // Don't show if it's just dashes or empty
                $cleanTitle = trim($title, '— -');
                $displayTitle = !empty($cleanTitle) ? $title : $topic->slug;
            @endphp
            <span class="font-semibold text-gray-900 text-lg">{{ $displayTitle }}</span>
            @if(!$topic->is_approved && $topic->user_id !== null)
                <span class="flex items-center gap-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-medium">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('topics.processing') }}
                </span>
            @endif
            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $keyword_count }} {{ __('topics.keywords') }}</span>
            <svg class="h-5 w-5 text-gray-400 ml-auto transition-transform duration-200 {{ $is_expanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Badges --}}
        @if($topic->user_id !== null)
            <span class="px-3 py-1 text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full border border-blue-200">
                {{ __('topics.custom') }}
            </span>
            <button
                type="button"
                wire:click="requestDeleteTopic({{ $topic->id }})"
                class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors duration-200"
                title="{{ __('topics.delete') }}"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        @else
            <span class="px-3 py-1 text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-full border border-gray-300">
                {{ __('topics.system') }}
            </span>
        @endif
    </div>

    {{-- Expanded content --}}
    @if($is_expanded)
        <div class="p-5 space-y-4 bg-gradient-to-br from-white to-gray-50 border-t border-gray-100">
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <h4 class="text-sm font-semibold text-gray-700">{{ __('topics.add_keyword') }}</h4>
                </div>
                @livewire('slug.create-keyword', ['topicId' => $topic->id], key('create-keyword-'.$topic->id))
            </div>

            @if($keyword_count > 0 || isset($creating_keywords_placeholders[$topic->id]))
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-700">{{ __('topics.keywords') }}</h4>
                    </div>
                    <div class="space-y-2">
                        {{-- Placeholder keywords being created --}}
                        @if(isset($creating_keywords_placeholders[$topic->id]))
                            @foreach($creating_keywords_placeholders[$topic->id] as $placeholder_id)
                                <div wire:key="keyword-placeholder-{{ $placeholder_id }}" class="group/kw flex items-center gap-3 px-4 py-2.5 rounded-lg bg-white border-2 border-yellow-200 animate-pulse">
                                    {{-- Visibility toggle skeleton --}}
                                    <div class="p-1.5 bg-gray-200 rounded w-8 h-8 animate-pulse"></div>

                                    {{-- Keyword name skeleton --}}
                                    <div class="flex-1 flex items-center gap-2">
                                        <div class="h-4 bg-gray-300 rounded w-32 animate-pulse"></div>
                                        <span class="flex items-center gap-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-medium">
                                            <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('topics.processing') }}
                                        </span>
                                    </div>

                                    {{-- Badge skeleton --}}
                                    <div class="h-6 w-16 bg-blue-200 rounded-md animate-pulse"></div>
                                </div>
                            @endforeach
                        @endif

                        {{-- Real keywords --}}
                        @foreach($topic->keywords as $kw)
                            @php
                                $kw_visible = in_array($kw->id, $visible_keyword_ids, true);
                            @endphp
                            <div wire:key="keyword-{{ $kw->id }}" class="group/kw flex items-center gap-3 px-4 py-2.5 rounded-lg bg-white border {{ $kw_visible ? 'border-green-200' : 'border-gray-200' }} hover:shadow-sm transition-all duration-200">
                                {{-- Visibility toggle --}}
                                <button 
                                    wire:click="toggleKeywordVisibility({{ $kw->id }})"
                                    class="p-1.5 rounded transition-all duration-200 {{ $kw_visible ? 'bg-green-50 hover:bg-green-100' : 'bg-gray-50 hover:bg-gray-100' }}"
                                    title="{{ $kw_visible ? __('topics.visible_in_chat') : __('topics.hidden_in_chat') }}"
                                >
                                    @if($kw_visible)
                                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    @endif
                                </button>

                                @php
                                    $kw_value = $kw->value($locale);
                                    // Don't show if it's just dashes or empty
                                    $clean_kw_value = trim($kw_value, '— -');
                                    $display_kw_value = !empty($clean_kw_value) ? $kw_value : $kw->slug;
                                @endphp
                                <div class="flex-1 flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-700">{{ $display_kw_value }}</span>
                                    @if(!$kw->is_approved && $kw->user_id !== null)
                                        <span class="flex items-center gap-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-medium">
                                            <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('topics.processing') }}
                                        </span>
                                    @endif
                                </div>

                                @if($kw->user_id !== null)
                                    <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-md border border-blue-200">
                                        {{ __('topics.custom') }}
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="requestDeleteKeyword({{ $kw->id }})"
                                        class="opacity-0 group-hover/kw:opacity-100 p-1 bg-red-100 text-red-600 rounded hover:bg-red-600 hover:text-white transition-all duration-200"
                                        title="{{ __('topics.delete') }}"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-gray-50 to-gray-100 text-gray-600 rounded-md border border-gray-200">
                                        {{ __('topics.system') }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-gray-400">
                    <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <p class="text-sm">{{ __('topics.no_keywords_yet') }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
