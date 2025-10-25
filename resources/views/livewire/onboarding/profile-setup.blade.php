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
                <option value="{{ $lang->value }}">
                    {{ $lang->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="block">{{ __('onboarding.learning_language') }}</label>
        <select wire:model="learning_lang" class="w-full border rounded p-2">
            @foreach($languages as $lang)
                <option value="{{ $lang->value }}">
                    {{ $lang->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('onboarding.save') }}
        </button>
    </div>
</div>
