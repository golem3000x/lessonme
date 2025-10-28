<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">{{ __('onboarding.profile_setup') }}</h2>

    <div class="mb-3">
        <label class="block">{{ __('onboarding.name') }}</label>
        <input type="text" wire:model.live="name" class="w-full border rounded p-2" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="mb-3">
            <label class="block">{{ __('onboarding.native_language') }}</label>
            <select wire:model="native_lang" class="w-full border rounded p-2">
                @foreach($languages as $lang)
                    <option value="{{ $lang->value }}">{{ $lang->label() }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block">{{ __('onboarding.learning_language') }}</label>
            <select wire:model="learning_lang" class="w-full border rounded p-2">
                @foreach($languages as $lang)
                    <option value="{{ $lang->value }}">{{ $lang->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="block">{{ __('onboarding.new_topic') }}</label>
        <input type="text" wire:model.live="new_topic_name" placeholder="{{ __('onboarding.enter_topic_name') }}" class="w-full border rounded p-2" />
    </div>

    <div class="mb-6">
        <label class="block font-medium mb-2">{{ __('onboarding.topics_visibility') ?? 'Topics visibility' }}</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($available_topics as $t)
                <div class="border rounded">
                    <label class="flex items-center gap-2 p-2 border-b">
                        <input type="checkbox" class="h-4 w-4" value="{{ $t['id'] }}" wire:model="visible_topic_ids">
                        <span class="font-medium">{{ $t['label'] }}</span>
                    </label>

                    <div class="p-2 space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="text" class="flex-1 border rounded p-2" placeholder="{{ __('onboarding.enter_keyword') ?? 'Add keyword' }}" wire:model.live="new_keyword_by_topic.{{ $t['id'] }}">
                            <button type="button" class="px-3 py-2 text-sm bg-gray-800 text-white rounded" wire:click="addKeyword({{ $t['id'] }})">{{ __('onboarding.add') ?? 'Add' }}</button>
                        </div>

                        @php($keywords = $topic_keywords[$t['id']] ?? [])
                        @if(count($keywords) > 0)
                            <div class="grid grid-cols-1 gap-1">
                                @foreach($keywords as $kw)
                                    <label class="flex items-center gap-2 px-2 py-1 rounded hover:bg-gray-50">
                                        <input type="checkbox" class="h-4 w-4" value="{{ $kw['id'] }}" wire:model="visible_keyword_ids">
                                        <span class="text-sm">{{ $kw['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">{{ __('onboarding.no_keywords_yet') ?? 'No keywords yet.' }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <p class="text-sm text-gray-500 mt-2">{{ __('onboarding.topics_visibility_hint') ?? 'Uncheck to hide a topic or keyword from Chat.' }}</p>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('onboarding.save') }}
        </button>
    </div>
</div>