<div class="space-y-4">
    <form wire:submit="sendMessage" class="flex gap-3">
        <input 
            type="text" 
            wire:model="new_message"
            placeholder="{{ __('admin.enter_message') }}"
            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            maxlength="1000"
        >
        
        <button 
            type="submit"
            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>{{ __('admin.send_button') }}</span>
            <span wire:loading>{{ __('admin.sending') }}</span>
        </button>
    </form>
    
    @error('new_message')
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
