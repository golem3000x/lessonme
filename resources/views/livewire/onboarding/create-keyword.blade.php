<div>
    <div class="flex items-center gap-3">
        <input 
            type="text" 
            class="flex-1 border-2 border-green-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 placeholder:text-gray-400" 
            placeholder="{{ __('onboarding.enter_keyword') ?? 'Add keyword' }}" 
            wire:model.live="keyword_value"
        />
        <button 
            type="button" 
            class="group px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center gap-2" 
            wire:click="saveKeyword"
            wire:loading.attr="disabled"
        >
            <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('onboarding.save') }}
        </button>
    </div>
    @error('keyword_value')
        <span class="text-red-600 text-sm block mt-2">{{ $message }}</span>
    @enderror
</div>
