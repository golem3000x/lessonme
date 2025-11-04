<div class="mb-3">
    <label class="block font-medium mb-2">{{ __('onboarding.new_topic') }}</label>
    <div class="flex items-center gap-2">
        <input 
            type="text" 
            wire:model.live="topic_name" 
            placeholder="{{ __('onboarding.enter_topic_name') }}" 
            class="flex-1 border rounded p-2"
        />
        <button 
            type="button" 
            wire:click="saveTopic" 
            class="px-3 py-2 text-sm bg-gray-800 text-white rounded hover:bg-gray-900"
            wire:loading.attr="disabled"
        >
            {{ __('onboarding.save') }}
        </button>
    </div>
    @error('topic_name')
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror
</div>
