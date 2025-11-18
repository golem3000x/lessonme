<div class="max-w-3xl mx-auto p-8 bg-white rounded-2xl shadow-lg">
    <!-- Заголовок -->
    <div class="flex items-center justify-between mb-6 border-b pb-3">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-6 w-6 text-blue-600"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5.121 17.804A3 3 0 016 17h12a3 3 0 01.879.804M12 14a5 5 0 100-10 5 5 0 000 10z"/>
            </svg>
            {{ __('onboarding.profile_setup') }}
        </h2>
    </div>

    <!-- Flash messages -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Name -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('onboarding.name') }}
        </label>
        <input type="text"
               wire:model.live="name"
               placeholder="{{ __('onboarding.enter_name') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition" />
        @error('name')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Language selectors -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('onboarding.native_language') }}
            </label>
            <select wire:model="native_lang"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition">
                @foreach($languages as $lang)
                    <option value="{{ $lang->value }}">{{ $lang->flag() }} {{ $lang->label() }}</option>
                @endforeach
            </select>
            @error('native_lang')
            <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('onboarding.learning_language') }}
            </label>
            <select wire:model="learning_lang"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition">
                @foreach($languages as $lang)
                    <option value="{{ $lang->value }}">{{ $lang->flag() }} {{ $lang->label() }}</option>
                @endforeach
            </select>
            @error('learning_lang')
            <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Save button -->
    <div class="flex justify-end">
        <button wire:click="save"
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"/>
            </svg>
            {{ __('onboarding.save') }}
        </button>
    </div>
</div>
