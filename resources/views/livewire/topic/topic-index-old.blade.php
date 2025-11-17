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
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm flex items-center gap-3">
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
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
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('topics.your_topics') }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('topics.visibility_description') }}</p>
                </div>
                <button
                        wire:click="saveVisibility"
                        class="group px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2"
                        wire:loading.attr="disabled"
                >
                    <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('topics.save_visibility') }}
                </button>
            </div>

            @if(count($available_topics) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($available_topics as $t)
                        <div class="group relative bg-white border-2 border-transparent rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                            {{-- Gradient border effect --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-pink-500 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
                            <div class="absolute inset-0.5 bg-white rounded-xl -z-10"></div>
                            
                            <div class="flex items-center gap-3 p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                    <input type="checkbox" class="h-5 w-5 text-orange-500 border-orange-300 rounded focus:ring-orange-500 focus:ring-2 cursor-pointer" value="{{ $t['id'] }}"
                                           wire:model="visible_topic_ids" title="{{ __('topics.visible_in_chat') }}">
                                    <span class="font-semibold text-gray-900 text-lg">{{ $t['label'] }}</span>
                                </label>
                                @if($t['user_id'] !== null)
                                    <span class="px-3 py-1 text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full border border-blue-200">
                                        {{ __('topics.custom') }}
                                    </span>
                                    <button
                                            type="button"
                                            wire:click="deleteTopic({{ $t['id'] }})"
                                            wire:confirm="{{ __('topics.confirm_delete_topic') }}"
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

                            <div class="p-5 space-y-4 bg-gradient-to-br from-white to-gray-50">
                                <div class="p-4 bg-white rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <h4 class="text-sm font-semibold text-gray-700">{{ __('topics.add_keyword') }}</h4>
                                    </div>
                                    @livewire('onboarding.create-keyword', ['topicId' => $t['id']],
                                    key('create-keyword-'.$t['id']))
                                </div>

                                @php($keywords = $topic_keywords[$t['id']] ?? [])
                                @if(count($keywords) > 0)
                                    <div>
                                        <div class="flex items-center gap-2 mb-3">
                                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <h4 class="text-sm font-semibold text-gray-700">{{ __('topics.keywords') }}</h4>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($keywords as $kw)
                                                <div class="group/kw flex items-center gap-3 px-4 py-2.5 rounded-lg bg-white border border-gray-200 hover:border-green-300 hover:shadow-sm transition-all duration-200">
                                                    <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                                        <input type="checkbox" class="h-4 w-4 text-green-500 border-green-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer" value="{{ $kw['id'] }}"
                                                               wire:model="visible_keyword_ids"
                                                               title="{{ __('topics.visible_in_chat') }}">
                                                        <span class="text-sm font-medium text-gray-700">{{ $kw['label'] }}</span>
                                                    </label>
                                                    @if($kw['user_id'] !== null)
                                                        <span class="px-2 py-1 text-xs font-medium bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-md border border-blue-200">
                                                            {{ __('topics.custom') }}
                                                        </span>
                                                        <button
                                                                type="button"
                                                                wire:click="deleteKeyword({{ $kw['id'] }})"
                                                                wire:confirm="{{ __('topics.confirm_delete_keyword') }}"
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
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                        <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <p class="text-lg font-medium text-gray-700">{{ __('topics.no_topics_yet') }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ __('topics.create_first_topic') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
