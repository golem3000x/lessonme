@php use App\Enums\System\LanguageLevelEnum; @endphp
<div class="space-y-5">
    <div class="flex flex-col space-y-5">
        {{-- Language Selection (only show if user has multiple learning languages) --}}
        @if(count($available_languages) > 1)
            <div class="group">
                <label for="lesson-language-select" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                    <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    {{ __('lessons.lesson_language') }}
                </label>
                <select
                        id="lesson-language-select"
                        wire:model.live="lesson_language"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-purple-400"
                >
                    @foreach($available_languages as $lang)
                        <option value="{{ $lang['value'] }}">{{ $lang['flag'] }} {{ $lang['label'] }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="group">
            <label for="language-level-select" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('lessons.level') }}
            </label>
            <select
                    id="language-level-select"
                    wire:model.live="language_level"
                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-blue-400"
            >
                @foreach(LanguageLevelEnum::cases() as $level)
                    <option value="{{ $level->value }}">{{ $level->label() }}</option>
                @endforeach
            </select>
            @if($language_level)
                @php
                    $selected_level = \App\Enums\System\LanguageLevelEnum::tryFrom($language_level);
                @endphp
                @if($selected_level)
                    <div class="mt-2 space-y-2">
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold text-blue-700">{{ $selected_level->label() }}:</span>
                                {{ $selected_level->description() }}
                            </p>
                        </div>

                        @if(in_array($selected_level->value, ['B1','B2','C1','C2'], true))
                            <div class="flex items-start gap-2 rounded-md border border-amber-300 bg-amber-50 px-3 py-2">
                                <svg class="mt-0.5 h-3.5 w-3.5 text-amber-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11a.75.75 0 00-1.5 0v4c0 .199.079.39.22.53l2.5 2.5a.75.75 0 101.06-1.06l-2.28-2.28V7z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-xs font-medium text-amber-800 leading-relaxed">
                                    {{ __('lessons.generation_time_notice') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        <div class="group">
            <label for="topic-select" class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ __('lessons.topic_label') }}
            </label>
            <select
                    id="topic-select"
                    wire:model.live="topic_id"
                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white hover:border-indigo-400"
            >
                @foreach($topics as $t)
                    <option value="{{ $t['id'] }}">{{ $t['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="group">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ __('lessons.keywords_label') }}
            </label>
            <livewire:lesson.keyword-select :topic_id="$topic_id" :keyword_ids="$keyword_ids" :keywords="$keywords"/>
        </div>

        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ __('lessons.generation_info') }}
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            <button
                    wire:click="$parent.closeManageModal"
                    class="group px-5 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium flex items-center gap-2"
                    wire:loading.attr="disabled"
            >
                <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('lessons.cancel') }}
            </button>
            <button
                    wire:click="createLesson"
                    wire:loading.attr="disabled"
                    wire:target="createLesson"
                    class="group bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-2.5 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 min-w-[160px] justify-center font-medium shadow-md hover:shadow-lg"
            >
                <span wire:loading.remove wire:target="createLesson" class="flex items-center gap-2">
                    <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('lessons.create_lesson') }}
                </span>
                <span wire:loading wire:target="createLesson" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm">{{ __('lessons.generating') }}</span>
                </span>
            </button>
        </div>
    </div>
</div>
