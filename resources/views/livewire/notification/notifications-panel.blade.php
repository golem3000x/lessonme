<div class="fixed top-20 right-6 z-50 space-y-3 max-w-sm">
    @foreach ($notifications as $n)
        @php
            $color_class = 'from-blue-500 to-indigo-600';
            $bg_class = 'from-blue-50 to-indigo-50';
            $border_class = 'border-blue-200';
            $text_class = 'text-blue-800';
            
            if(str_contains($n['status'], 'success') || str_contains($n['status'], 'approved')) {
                $color_class = 'from-green-500 to-emerald-600';
                $bg_class = 'from-green-50 to-emerald-50';
                $border_class = 'border-green-200';
                $text_class = 'text-green-800';
            } elseif(str_contains($n['status'], 'rejected') || str_contains($n['status'], 'error')) {
                $color_class = 'from-red-500 to-pink-600';
                $bg_class = 'from-red-50 to-pink-50';
                $border_class = 'border-red-200';
                $text_class = 'text-red-800';
            } elseif(str_contains($n['status'], 'processing')) {
                $color_class = 'from-yellow-500 to-orange-600';
                $bg_class = 'from-yellow-50 to-orange-50';
                $border_class = 'border-yellow-200';
                $text_class = 'text-yellow-800';
            } elseif($n['status'] === 'welcome' || $n['status'] === 'registered') {
                $color_class = 'from-purple-500 to-pink-600';
                $bg_class = 'from-purple-50 to-pink-50';
                $border_class = 'border-purple-200';
                $text_class = 'text-purple-800';
            }
        @endphp
        
        <div
                wire:key="notif-{{ $n['id'] }}"
                x-data="{ show: true, progress: 100 }"
                x-init="
                    let interval = setInterval(() => { 
                        progress -= 2; 
                        if (progress <= 0) clearInterval(interval); 
                    }, 100);
                    setTimeout(() => { 
                        show = false; 
                        setTimeout(() => $wire.removeNotification('{{ $n['id'] }}'), 500); 
                    }, 5000)
                "
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-full"
                class="relative bg-gradient-to-r {{ $bg_class }} border-2 {{ $border_class }} shadow-xl rounded-xl overflow-hidden cursor-pointer group hover:shadow-2xl transition-all duration-200"
                @click="show = false; $wire.removeNotification('{{ $n['id'] }}')"
        >
            <div class="flex items-start gap-3 p-4">
                {{-- Icon --}}
                <div class="flex-shrink-0 p-2 bg-gradient-to-br {{ $color_class }} rounded-lg shadow-md">
                    @if(str_contains($n['status'], 'success') || str_contains($n['status'], 'approved'))
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(str_contains($n['status'], 'rejected') || str_contains($n['status'], 'error'))
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(str_contains($n['status'], 'processing'))
                        <svg class="h-5 w-5 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    @elseif($n['status'] === 'welcome' || $n['status'] === 'registered')
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="font-bold {{ $text_class }} text-sm mb-1">{{ ucfirst($n['entity']) }}</div>
                    <div class="text-sm text-gray-700 leading-relaxed">{{ $n['message'] }}</div>
                    @if(!empty($n['lesson_url']) && (str_contains($n['status'], 'success') || $n['status'] === 'exists'))
                        <a href="{{ $n['lesson_url'] }}" 
                           class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 mt-2 hover:underline"
                           @click.stop>
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ __('lessons.view_lesson') }}
                        </a>
                    @endif
                </div>

                {{-- Close button --}}
                <button
                        type="button"
                        class="flex-shrink-0 p-1 text-gray-400 hover:text-gray-600 rounded transition-colors opacity-0 group-hover:opacity-100"
                        @click.stop="show = false; $wire.removeNotification('{{ $n['id'] }}')"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Progress bar --}}
            <div class="h-1 bg-gray-200">
                <div 
                    class="h-full bg-gradient-to-r {{ $color_class }} transition-all duration-100 ease-linear"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
        </div>
    @endforeach
</div>