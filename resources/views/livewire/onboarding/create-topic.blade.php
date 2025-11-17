<div>
    <label class="flex items-center gap-2 text-sm font-semibold text-blue-900 mb-2">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        {{ __('onboarding.new_topic') }}
    </label>
    <div class="flex items-center gap-3">
        <input 
            type="text" 
            wire:model.live="topic_name" 
            placeholder="{{ __('onboarding.enter_topic_name') }}" 
            class="flex-1 border-2 border-blue-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder:text-gray-400"
        />
        <button 
            type="button" 
            wire:click="saveTopic" 
            class="group px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center gap-2"
            wire:loading.attr="disabled"
        >
            <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ __('onboarding.save') }}
        </button>
    </div>
    @error('topic_name')
        <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
