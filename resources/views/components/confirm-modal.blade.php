@props([
    'icon' => 'warning',
    'title',
    'message',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmRoute' => null,
    'confirmMethod' => 'POST',
    'showVariable' => 'showModal',
    'iconColor' => null,
    'buttonColor' => null,
])

@php
    // Определяем цвета на основе типа иконки
    $iconConfig = match($icon) {
        'danger', 'delete' => [
            'bg' => 'from-red-100 to-pink-100',
            'icon_color' => 'text-red-600',
            'button_bg' => 'from-red-500 to-pink-500',
            'button_hover' => 'from-red-600 to-pink-600',
            'icon_path' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        ],
        'warning', 'regenerate' => [
            'bg' => 'from-yellow-100 to-orange-100',
            'icon_color' => 'text-yellow-600',
            'button_bg' => 'from-yellow-500 to-orange-500',
            'button_hover' => 'from-yellow-600 to-orange-600',
            'icon_path' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        ],
        'success', 'restore' => [
            'bg' => 'from-green-100 to-emerald-100',
            'icon_color' => 'text-green-600',
            'button_bg' => 'from-green-500 to-emerald-500',
            'button_hover' => 'from-green-600 to-emerald-600',
            'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'info' => [
            'bg' => 'from-blue-100 to-indigo-100',
            'icon_color' => 'text-blue-600',
            'button_bg' => 'from-blue-500 to-indigo-500',
            'button_hover' => 'from-blue-600 to-indigo-600',
            'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        default => [
            'bg' => 'from-gray-100 to-gray-200',
            'icon_color' => 'text-gray-600',
            'button_bg' => 'from-gray-500 to-gray-600',
            'button_hover' => 'from-gray-600 to-gray-700',
            'icon_path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    };

    // Переопределяем цвета, если они заданы явно
    if ($iconColor) {
        $iconConfig['bg'] = $iconColor;
    }
    if ($buttonColor) {
        $iconConfig['button_bg'] = $buttonColor;
    }
@endphp

<div
    x-show="{{ $showVariable }}"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="{{ $showVariable }} = false"
>
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         x-show="{{ $showVariable }}"
         x-transition
    ></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100"
            x-show="{{ $showVariable }}"
            x-transition
            @click.stop
        >
            {{-- Icon --}}
            <div class="flex justify-center pt-8 pb-4">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br {{ $iconConfig['bg'] }} rounded-full">
                    <svg class="h-10 w-10 {{ $iconConfig['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconConfig['icon_path'] }}" />
                    </svg>
                </div>
            </div>

            {{-- Content --}}
            <div class="px-6 pb-6 text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-3">
                    {{ $title }}
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    {{ $message }}
                </p>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button
                        @click="{{ $showVariable }} = false"
                        class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200"
                    >
                        {{ $cancelText }}
                    </button>
                    
                    @if($confirmRoute)
                        <form method="POST" action="{{ $confirmRoute }}" class="flex-1">
                            @csrf
                            @method($confirmMethod)
                            <button
                                type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r {{ $iconConfig['button_bg'] }} text-white font-semibold rounded-xl hover:{{ $iconConfig['button_hover'] }} shadow-md hover:shadow-lg transition-all duration-200"
                            >
                                {{ $confirmText }}
                            </button>
                        </form>
                    @else
                        {{-- Для случаев без route (например, wire:click) --}}
                        {{ $slot }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
