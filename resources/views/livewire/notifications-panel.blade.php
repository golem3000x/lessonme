<div class="fixed top-4 right-4 z-50 space-y-3">
    @foreach ($notifications as $n)
        <div
                wire:key="notif-{{ $n['id'] }}"
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                x-on:click.outside="show = false"
                @click="show = false; Livewire.dispatch('removeNotification', { id: '{{ $n['id'] }}' })"
                class="relative bg-white border shadow-lg rounded-xl px-4 py-3 flex items-start space-x-3 transition transform duration-300 cursor-pointer"
        >
            <div class="text-lg">
                @if(str_contains($n['status'], 'success'))
                    ✅
                @elseif(str_contains($n['status'], 'approved'))
                    🟢
                @elseif(str_contains($n['status'], 'rejected'))
                    🔴
                @else
                    🔔
                @endif
            </div>

            <div class="text-sm flex-1">
                <div class="font-semibold">{{ $n['entity'] }}</div>
                <div>{{ __($n['message']) }}</div>
            </div>

            <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 absolute top-2 right-2"
                    @click.stop="show = false; Livewire.dispatch('removeNotification', { id: '{{ $n['id'] }}' })"
            >
                ✖
            </button>
        </div>
    @endforeach
</div>