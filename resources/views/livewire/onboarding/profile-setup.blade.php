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

    <!-- Avatar Upload -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">
            {{ __('onboarding.avatar') }}
        </label>
        <div class="flex items-center gap-4">
            <!-- Current Avatar or Placeholder -->
            <div class="relative">
                @if($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" 
                         alt="Preview" 
                         class="h-20 w-20 rounded-full object-cover border-2 border-blue-400">
                @elseif(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                         alt="Avatar" 
                         class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Upload/Remove Buttons -->
            <div class="flex flex-col gap-2">
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    {{ __('onboarding.upload_avatar') }}
                    <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                </label>

                @if(auth()->user()->avatar || $avatar)
                    <button wire:click="removeAvatar" 
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('onboarding.remove_avatar') }}
                    </button>
                @endif
            </div>
        </div>
        
        @error('avatar')
            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
        @enderror

        <!-- Loading indicator -->
        <div wire:loading wire:target="avatar" class="mt-2 text-sm text-blue-600 flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('onboarding.uploading') }}...
        </div>
    </div>

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
