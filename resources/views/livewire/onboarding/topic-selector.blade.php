<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">{{ __('onboarding.choose_interests') }}</h2>
    <div class="grid grid-cols-2 gap-2">
        @foreach($topics as $t)
            @php
                $key = 'topics.' . ($t->slug ?? '');
                $translated = __($key);
                $label = $translated !== $key ? $translated : ($t->slug ?? '');
            @endphp
            <label class="p-3 border rounded">
                <input type="checkbox" wire:model="selected" value="{{ $t->slug }}" /> {{ $label }}
            </label>
        @endforeach
    </div>
    <div class="mt-4 flex justify-end">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">{{ __('onboarding.save') }}</button>
    </div>
</div>
