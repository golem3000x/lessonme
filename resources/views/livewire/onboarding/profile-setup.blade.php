<div class="max-w-3xl mx-auto p-8 bg-white rounded-2xl shadow-lg">
    <!-- Header -->
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
                    <img src="{{ auth()->user()->avatar_url }}" 
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

    <!-- Name Fields -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('onboarding.first_name') }}
            </label>
            <input type="text"
                   wire:model.live="first_name"
                   placeholder="{{ __('onboarding.first_name_placeholder') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition" />
            @error('first_name')
            <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('onboarding.last_name') }}
            </label>
            <input type="text"
                   wire:model.live="last_name"
                   placeholder="{{ __('onboarding.last_name_placeholder') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition" />
            @error('last_name')
            <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Date of Birth -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('onboarding.date_of_birth') }}
        </label>
        <input type="date"
               wire:model="date_of_birth"
               max="{{ now()->subYears(5)->format('Y-m-d') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition" />
        @error('date_of_birth')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Bio -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('onboarding.bio') }}
        </label>
        <textarea wire:model="bio"
                  rows="3"
                  maxlength="500"
                  placeholder="{{ __('onboarding.bio_placeholder') }}"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-blue-500 transition resize-none"></textarea>
        <p class="text-xs text-gray-500 mt-1">{{ strlen($bio ?? '') }}/500</p>
        @error('bio')
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

        @if($is_student)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('onboarding.learning_languages') }}
            </label>
            <div x-data="{ open: false }" class="relative">
                <button 
                    type="button" 
                    @click="open = !open" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white flex items-center justify-between hover:border-blue-400 transition focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <span class="text-sm text-gray-700">{{ __('auth.role_selection.select_languages') }}</span>
                    <div class="flex items-center gap-2">
                        @if(count($learning_languages) > 0)
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ count($learning_languages) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">0</span>
                        @endif
                        <svg class="h-4 w-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
                <div 
                    x-cloak 
                    x-show="open" 
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 right-0 z-50 mt-1 rounded-lg border border-gray-200 bg-white shadow-lg"
                >
                    <div class="p-2 space-y-1">
                        @foreach($languages as $lang)
                            <label class="flex items-center space-x-3 p-2 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                <input 
                                    type="checkbox" 
                                    value="{{ $lang->value }}"
                                    wire:model.live="learning_languages"
                                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="text-sm text-gray-800">{{ $lang->flag() }} {{ $lang->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('learning_languages')
            <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
        @endif
    </div>

    <!-- Role-specific settings -->
    @if($is_student)
    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            {{ __('onboarding.student_settings') }}
        </h3>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" 
                   wire:model="is_looking_for_teacher"
                   class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="text-sm text-gray-700">{{ __('onboarding.looking_for_teacher') }}</span>
        </label>
        <p class="text-xs text-gray-500 mt-2 ml-8">{{ __('onboarding.looking_for_teacher_hint') }}</p>
    </div>
    @endif

    @if($is_teacher)
    <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
        <h3 class="text-sm font-semibold text-green-800 mb-3 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            {{ __('onboarding.teacher_settings') }}
        </h3>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" 
                   wire:model="is_accepting_students"
                   class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
            <span class="text-sm text-gray-700">{{ __('onboarding.accepting_students') }}</span>
        </label>
        <p class="text-xs text-gray-500 mt-2 ml-8">{{ __('onboarding.accepting_students_hint') }}</p>
    </div>
    @endif

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
