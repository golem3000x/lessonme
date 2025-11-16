<div class="fixed top-4 right-4 z-50 space-y-3">
    @foreach ($notifications as $n)
        <div
                wire:key="notif-{{ $n['id'] }}"
                x-data="{ show: true }"
                x-init="setTimeout(() => { show = false; setTimeout(() => $wire.removeNotification('{{ $n['id'] }}'), 500); }, 5000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                x-on:click.outside="show = false"
                @click="show = false; $wire.removeNotification('{{ $n['id'] }}')"
                class="relative bg-white border shadow-lg rounded-xl px-4 py-3 flex items-start space-x-3 transition transform duration-300 cursor-pointer"
        >
            <div class="text-lg">
                @if(str_contains($n['status'], 'success'))
                    ✅
                @elseif(str_contains($n['status'], 'approved'))
                    🟢
                @elseif(str_contains($n['status'], 'rejected'))
                    🔴
                @elseif(str_contains($n['status'], 'processing'))
                    ⏳
                @elseif(str_contains($n['status'], 'error'))
                    ❌
                @elseif($n['status'] === 'welcome')
                    👋
                @elseif($n['status'] === 'registered')
                    🎉
                @else
                    🔔
                @endif
            </div>

            <div class="text-sm flex-1">
                <div class="font-semibold">{{ ucfirst($n['entity']) }}</div>
                <div>{{ $n['message'] }}</div>
                @if(!empty($n['lesson_url']) && (str_contains($n['status'], 'success') || $n['status'] === 'exists'))
                    <a href="{{ $n['lesson_url'] }}" 
                       class="text-blue-600 hover:text-blue-800 underline text-xs mt-1 inline-block"
                       @click.stop>
                        {{ __('lessons.view_lesson') }}
                    </a>
                @endif
            </div>

            <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 absolute top-2 right-2"
                    @click.stop="show = false; $wire.removeNotification('{{ $n['id'] }}')"
            >
                ✖
            </button>
        </div>
    @endforeach
</div>