@php use Illuminate\Support\Str; @endphp
<div class="max-w-6xl mx-auto p-6 space-y-8">
    {{-- Заголовок --}}
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            {{ __('lessons.my_lessons') }}
        </h2>

        @if(!$lessons->isEmpty())
            <button
                    wire:click="openManageModal"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('lessons.create_new_lesson') }}
            </button>
        @endif
    </div>

    {{-- Flash messages --}}
    @if (session()->has('message'))
        <div class="flex items-center gap-2 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="flex items-center gap-2 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Пустой список --}}
    @if($lessons->isEmpty())
        <div class="text-center py-16 space-y-4">
            <svg class="mx-auto h-14 w-14 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_lessons_yet') }}</h3>
            <p class="text-sm text-gray-500">{{ __('lessons.get_started_message') }}</p>
            <button wire:click="openManageModal"
                    class="mt-4 inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                          clip-rule="evenodd"/>
                </svg>
                {{ __('lessons.create_new_lesson') }}
            </button>
        </div>
    @else
        {{-- Список уроков --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($lessons as $lesson)
                <div class="group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:-translate-y-1 transition p-5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/20 to-transparent opacity-0 group-hover:opacity-100 transition pointer-events-none"></div>

                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-800 line-clamp-2 mb-2">{{ $lesson->lesson_name }}</h3>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                @if($lesson->language_level)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lesson->level_badge_color }}">
                                        {{ $lesson->language_level->value }}
                                    </span>
                                @endif
                                
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $lesson->formatted_created_at }}
                                </span>
                            </div>
                        </div>

                        <div class="relative ml-2">
                            <!-- Кнопка с тремя точками -->
                            <button wire:click="toggleDropdown({{ $lesson->id }})"
                                    class="text-gray-500 hover:text-gray-700 transition"
                                    title="{{ __('lessons.actions') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 12a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2zm6 0a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            @if(!empty($open_dropdowns[$lesson->id]))
                            <div x-data @click.outside="$wire.closeDropdown({{ $lesson->id }})" class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50">

                                <button wire:click="regenerateLesson({{ $lesson->id }})"
                                        wire:confirm="{{ __('lessons.confirm_regenerate') }}"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 whitespace-nowrap transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v6h6M20 20v-6h-6M4 10a9 9 0 0115.9-4.9M20 14a9 9 0 01-15.9 4.9"/>
                                    </svg>
                                    {{ __('lessons.regenerate_lesson') }}
                                </button>

                                <a href="{{ route('lessons.history', $lesson->id) }}"
                                   class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('lessons.history') }}
                                </a>

                                <button wire:click="deleteLesson({{ $lesson->id }})"
                                        wire:confirm="{{ __('lessons.confirm_delete') }}"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    {{ __('lessons.delete') }}
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-4 leading-snug">{{ Str::limit($lesson->lesson_text, 120) }}</p>

                    <a href="{{ route('lessons.show', $lesson->id) }}"
                       class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14 5l7 7-7 7M3 12h18"/>
                        </svg>
                        {{ __('lessons.view_lesson') }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal --}}
    @if($show_manage_modal)
        <div
                x-data="{ open: true }"
                x-show="open"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="$wire.closeManageModal()"
        >
            {{-- Фон с размытием --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

            {{-- Центрируем модальное окно --}}
            <div class="flex items-center justify-center min-h-screen p-4">
                <div
                        class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all"
                        x-transition.scale
                        @click.stop
                >
                    <div class="p-6 border-b flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900">{{ __('lessons.manage_lesson') }}</h3>
                        <button wire:click="closeManageModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <livewire:lesson.manage-lesson :key="'manage-lesson-'.now()->timestamp"/>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('close-manage-modal', () => $wire.closeManageModal());
</script>
@endscript