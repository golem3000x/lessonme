<div class="flex items-center gap-2">
    <input 
        type="text" 
        class="flex-1 border rounded p-2" 
        placeholder="{{ __('onboarding.enter_keyword') ?? 'Add keyword' }}" 
        wire:model.live="keyword_value"
    />
    <button 
        type="button" 
        class="px-3 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-900" 
        wire:click="saveKeyword"
        wire:loading.attr="disabled"
    >
        {{ __('onboarding.save') }}
    </button>
</div>
@error('keyword_value')
    <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
@enderror
