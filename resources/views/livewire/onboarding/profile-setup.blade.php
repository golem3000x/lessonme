<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">{{ __('onboarding.profile_setup') }}</h2>

    <div class="mb-3">
        <label class="block">{{ __('onboarding.name') }}</label>
        <input type="text" wire:model="name" class="w-full border rounded p-2" />
    </div>

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

    <div class="mb-3">
        <label class="block">{{ __('onboarding.new_topic') }}</label>
        <input type="text" wire:model="new_topic_name" placeholder="{{ __('onboarding.enter_topic_name') }}" class="w-full border rounded p-2" />
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-2">{{ __('onboarding.topics_visibility') ?? 'Topics visibility' }}</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($available_topics as $t)
                <label class="flex items-center gap-2 p-2 border rounded">
                    <input type="checkbox" class="h-4 w-4" value="{{ $t['id'] }}" wire:model="visible_topic_ids">
                    <span>{{ $t['label'] }}</span>
                </label>
            @endforeach
        </div>
        <p class="text-sm text-gray-500 mt-1">{{ __('onboarding.topics_visibility_hint') ?? 'Uncheck to hide a topic from Chat.' }}</p>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('onboarding.save') }}
        </button>
    </div>
</div>