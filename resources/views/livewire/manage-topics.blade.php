<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">{{ __('topics.manage_topics') }}</h2>
            <a href="{{ route('onboarding.profile') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('topics.back_to_profile') }}
            </a>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-3">{{ __('topics.create_new_topic') }}</h3>
            @livewire('onboarding.create-topic')
        </div>

        <div class="border-t pt-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium">{{ __('topics.your_topics') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ __('topics.visibility_description') }}</p>
                </div>
                <button 
                    wire:click="saveVisibility" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                    wire:loading.attr="disabled"
                >
                    {{ __('topics.save_visibility') }}
                </button>
            </div>
            
            @if(count($available_topics) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($available_topics as $t)
                        <div class="border rounded-lg">
                            <div class="flex items-center gap-2 p-4 border-b bg-gray-50">
                                <input type="checkbox" class="h-4 w-4" value="{{ $t['id'] }}" wire:model="visible_topic_ids" title="{{ __('topics.visible_in_chat') }}">
                                <span class="font-medium flex-1">{{ $t['label'] }}</span>
                                @if($t['user_id'] !== null)
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                        {{ __('topics.custom') }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click="deleteTopic({{ $t['id'] }})"
                                        wire:confirm="{{ __('topics.confirm_delete_topic') }}"
                                        class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                                    >
                                        {{ __('topics.delete') }}
                                    </button>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                        {{ __('topics.system') }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-4 space-y-3">
                                <div>
                                    <h4 class="text-sm font-medium mb-2">{{ __('topics.add_keyword') }}</h4>
                                    @livewire('onboarding.create-keyword', ['topicId' => $t['id']], key('create-keyword-'.$t['id']))
                                </div>

                                @php($keywords = $topic_keywords[$t['id']] ?? [])
                                @if(count($keywords) > 0)
                                    <div>
                                        <h4 class="text-sm font-medium mb-2">{{ __('topics.keywords') }}</h4>
                                        <div class="space-y-1">
                                            @foreach($keywords as $kw)
                                                <div class="flex items-center gap-2 px-3 py-2 rounded bg-gray-50 hover:bg-gray-100">
                                                    <input type="checkbox" class="h-4 w-4" value="{{ $kw['id'] }}" wire:model="visible_keyword_ids" title="{{ __('topics.visible_in_chat') }}">
                                                    <span class="text-sm flex-1">{{ $kw['label'] }}</span>
                                                    @if($kw['user_id'] !== null)
                                                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                                            {{ __('topics.custom') }}
                                                        </span>
                                                        <button 
                                                            type="button" 
                                                            wire:click="deleteKeyword({{ $kw['id'] }})"
                                                            wire:confirm="{{ __('topics.confirm_delete_keyword') }}"
                                                            class="px-2 py-0.5 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                                                        >
                                                            {{ __('topics.delete') }}
                                                        </button>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-800 rounded">
                                                            {{ __('topics.system') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">{{ __('topics.no_keywords_yet') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>{{ __('topics.no_topics_yet') }}</p>
                    <p class="text-sm mt-2">{{ __('topics.create_first_topic') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
