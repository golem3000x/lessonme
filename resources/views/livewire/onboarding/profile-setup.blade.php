<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow">
    <div class="mb-4">
        <h2 class="text-xl font-semibold">{{ __('onboarding.profile_setup') }}</h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-3">
        <label class="block">{{ __('onboarding.name') }}</label>
        <input type="text" wire:model.live="name" class="w-full border rounded p-2" />
        @error('name')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="mb-3">
            <label class="block">{{ __('onboarding.native_language') }}</label>
            <select wire:model="native_lang" class="w-full border rounded p-2">
                @foreach($languages as $lang)
                    <option value="{{ $lang->value }}">{{ $lang->label() }}</option>
                @endforeach
            </select>
            @error('native_lang')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="block">{{ __('onboarding.learning_language') }}</label>
            <select wire:model="learning_lang" class="w-full border rounded p-2">
                @foreach($languages as $lang)
                    <option value="{{ $lang->value }}">{{ $lang->label() }}</option>
                @endforeach
            </select>
            @error('learning_lang')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
        <p class="text-sm text-gray-700">
            {{ __('onboarding.next_step_message') }}
        </p>
    </div>

    <div class="flex justify-end gap-3">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('onboarding.save') }}
        </button>
    </div>
</div>