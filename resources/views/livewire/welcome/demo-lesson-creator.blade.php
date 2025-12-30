<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">{{ __('welcome.create_demo_lesson') }}</h2>

        <!-- Language Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            {{-- Native Language --}}
            <div x-data="{ open: false }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ __('lessons.native_language') }}
                </label>
                <div class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white flex items-center justify-between hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('native_lang') border-red-500 @enderror">
                        <span class="flex items-center gap-2">
                            <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/{{ $native_lang === 'vi' ? '1f1fb-1f1f3' : '1f1ec-1f1e7' }}.png" class="w-5 h-5" alt="">
                            <span>{{ \App\Enums\System\SystemLanguageEnum::tryFrom($native_lang)?->label() }}</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                        @foreach(\App\Enums\System\SystemLanguageEnum::learnable() as $lang)
                            <button type="button" wire:click="$set('native_lang', '{{ $lang->value }}')" @click="open = false"
                                    class="w-full px-4 py-2 flex items-center gap-2 hover:bg-blue-50 {{ $native_lang === $lang->value ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                                <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/{{ $lang->value === 'vi' ? '1f1fb-1f1f3' : '1f1ec-1f1e7' }}.png" class="w-5 h-5" alt="">
                                <span>{{ $lang->label() }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @error('native_lang')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Learning Language --}}
            <div x-data="{ open: false }">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ __('lessons.learning_language') }}
                </label>
                <div class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white flex items-center justify-between hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('learning_lang') border-red-500 @enderror">
                        <span class="flex items-center gap-2">
                            <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/{{ $learning_lang === 'vi' ? '1f1fb-1f1f3' : '1f1ec-1f1e7' }}.png" class="w-5 h-5" alt="">
                            <span>{{ \App\Enums\System\SystemLanguageEnum::tryFrom($learning_lang)?->label() }}</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                        @foreach(\App\Enums\System\SystemLanguageEnum::learnable() as $lang)
                            <button type="button" wire:click="$set('learning_lang', '{{ $lang->value }}')" @click="open = false"
                                    class="w-full px-4 py-2 flex items-center gap-2 hover:bg-blue-50 {{ $learning_lang === $lang->value ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                                <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/{{ $lang->value === 'vi' ? '1f1fb-1f1f3' : '1f1ec-1f1e7' }}.png" class="w-5 h-5" alt="">
                                <span>{{ $lang->label() }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @error('learning_lang')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Topic Selection -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('lessons.topic') }}
            </label>
            <select wire:model.live="topic_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($topics as $t)
                    <option value="{{ $t['id'] }}">{{ $t['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Keywords Selection -->
        @if(!empty($keywords))
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        {{ __('lessons.keywords') }}
                    </label>
                    <span class="text-sm text-gray-500">{{ __('lessons.optional') }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($keywords as $keyword)
                        <label class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition">
                            <input
                                    type="checkbox"
                                    wire:model.live="keyword_ids"
                                    value="{{ $keyword['id'] }}"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="text-sm text-gray-700">{{ $keyword['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Level Information (A1 Only) -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('lessons.language_level') }}
            </label>
            @php
                $a1_level = \App\Enums\System\LanguageLevelEnum::A1;
            @endphp
            <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                <p class="text-base text-gray-800 mb-2">
                    <span class="font-bold text-blue-700 text-lg">{{ $a1_level->label() }}</span>
                </p>
                <p class="text-sm text-gray-700">
                    {{ $a1_level->description() }}
                </p>
            </div>
        </div>

        <!-- Submit Button - only show if no lesson exists -->
        @if(!$show_success_message && !$pending_lesson_id)
            <div class="mt-8">
                <button
                        wire:click="createDemoLesson"
                        wire:loading.attr="disabled"
                        class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2"
                        @if($is_generating) disabled @endif
                >
                    @if($is_generating)
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('lessons.generating') }}...</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>{{ __('welcome.create_demo_lesson') }}</span>
                    @endif
                </button>
            </div>
        @endif

        <!-- Processing Message with Polling -->
        @if($pending_lesson_id && !$show_success_message && !$show_error_message)
            <div wire:poll.3s="checkLessonStatus"
                 class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-3">
                <svg class="animate-spin h-6 w-6 text-blue-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div>
                    <h3 class="font-bold text-blue-900">{{ __('lessons.lesson_creation_in_progress') }}</h3>
                    <p class="text-sm text-blue-700 mt-1">{{ __('welcome.demo_lesson_info') }}</p>
                </div>
            </div>
        @endif

        <!-- Success Message with Link -->
        @if($show_success_message && $demo_lesson_url)
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start gap-3 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 flex-shrink-0" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-bold text-green-900">{{ __('welcome.demo_lesson_ready') }}</h3>
                        @if($demo_lesson_name)
                            <p class="text-sm text-green-700 mt-1">{{ $demo_lesson_name }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ $demo_lesson_url }}" target="_blank"
                   class="block w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('lessons.view_lesson') }}
                </a>
            </div>
        @endif

        <!-- Error Message with Retry -->
        @if($show_error_message && $error_message)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-3 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 flex-shrink-0" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-900">{{ __('lessons.creation_failed') }}</h3>
                        <p class="text-sm text-red-700 mt-1">{{ $error_message }}</p>
                    </div>
                </div>
                <button
                        wire:click="retryCreation"
                        class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('lessons.try_again') }}
                </button>
            </div>
        @endif

        <!-- Temporary Error Message (from dispatch) -->
        <div
                x-data="{ show: false, message: '' }"
                x-on:show-error.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition
                class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3"
                style="display: none;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 flex-shrink-0" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm text-red-700" x-text="message"></span>
        </div>
    </div>
</div>
