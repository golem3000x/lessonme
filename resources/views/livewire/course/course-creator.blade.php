@php use App\Enums\System\LanguageLevelEnum; @endphp

<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('courses.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $course ? __('lessons.edit_course') : __('lessons.create_course') }}
            </h1>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.course_title') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="title"
                           class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="{{ __('lessons.course_title_placeholder') }}">
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.course_description') }}
                    </label>
                    <textarea wire:model="description" rows="3"
                              class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"
                              placeholder="{{ __('lessons.course_description_placeholder') }}"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Language --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('lessons.lesson_language') }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="lesson_lang" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @foreach($available_languages as $lang)
                                <option value="{{ $lang['value'] }}">{{ $lang['flag'] }} {{ $lang['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Level --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('lessons.level') }}
                        </label>
                        <select wire:model="language_level" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">{{ __('lessons.any_level') }}</option>
                            @foreach(LanguageLevelEnum::cases() as $level)
                                <option value="{{ $level->value }}">{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Passing Score --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('lessons.passing_score') }} (%)
                        </label>
                        <input type="number" wire:model="passing_score" min="0" max="100"
                               class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <p class="text-xs text-gray-500 mt-1">{{ __('lessons.passing_score_hint') }}</p>
                    </div>
                </div>

                {{-- Course Lessons (only in edit mode) --}}
                @if($course && !empty($course_lessons))
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-3">{{ __('lessons.course_lessons') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ __('lessons.drag_to_reorder') }}</p>
                        
                        <div class="space-y-2">
                            @foreach($course_lessons as $index => $lesson)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="text-sm font-medium text-gray-500 w-6">{{ $index + 1 }}.</span>
                                    <span class="flex-1 text-gray-900">{{ $lesson['name'] }}</span>
                                    <div class="flex items-center gap-1">
                                        <button wire:click="moveLessonUp({{ $index }})"
                                                class="p-1.5 text-gray-500 hover:bg-gray-200 rounded transition {{ $index === 0 ? 'opacity-30 cursor-not-allowed' : '' }}"
                                                {{ $index === 0 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="moveLessonDown({{ $index }})"
                                                class="p-1.5 text-gray-500 hover:bg-gray-200 rounded transition {{ $index === count($course_lessons) - 1 ? 'opacity-30 cursor-not-allowed' : '' }}"
                                                {{ $index === count($course_lessons) - 1 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="removeLessonFromCourse({{ $lesson['id'] }})"
                                                class="p-1.5 text-red-500 hover:bg-red-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-sm text-gray-500 mt-4">
                            {{ __('lessons.add_to_course') }}: {{ __('lessons.add_students_hint') }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <a href="{{ route('courses.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition font-medium">
                    {{ __('lessons.cancel') }}
                </a>
                <button wire:click="saveCourse" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-medium shadow-md">
                    {{ $course ? __('lessons.course_saved') : __('lessons.create_course') }}
                </button>
            </div>
        </div>
    </div>
</div>
