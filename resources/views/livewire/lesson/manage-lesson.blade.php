@php use App\Enums\LanguageLevelEnum; @endphp
<div class="space-y-4">
    <div class="flex flex-col space-y-4">
        <div>
            <label for="language-level-select" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('lessons.level') }}
            </label>
            <select
                    id="language-level-select"
                    wire:model.live="language_level"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
            >
                @foreach(LanguageLevelEnum::cases() as $level)
                    <option value="{{ $level->value }}">{{ $level->value }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="topic-select" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('lessons.topic_label') }}
            </label>
            <select
                    id="topic-select"
                    wire:model.live="topic_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
            >
                @foreach($topics as $t)
                    <option value="{{ $t['id'] }}">{{ $t['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('lessons.keywords_label') }}
            </label>
            <livewire:chat.keyword-select :topic_id="$topic_id" :keyword_ids="$keyword_ids" :keywords="$keywords"
                                          :key="'keyword-select-'.$topic_id"/>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <p class="text-sm text-gray-700">
                {{ __('lessons.generation_info') }}
            </p>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button
                    wire:click="$parent.closeManageModal"
                    class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition"
                    wire:loading.attr="disabled"
            >
                {{ __('lessons.cancel') }}
            </button>
            <button
                    wire:click="createLesson"
                    wire:loading.attr="disabled"
                    wire:target="createLesson"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 min-w-[140px] justify-center"
            >
                <span wire:loading.remove wire:target="createLesson">{{ __('lessons.create_lesson') }}</span>
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
