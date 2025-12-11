<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('lessons.my_courses') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('lessons.no_courses_hint') }}</p>
            </div>
            <a href="{{ route('courses.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 font-medium shadow-md transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('lessons.create_course') }}
            </a>
        </div>

        {{-- Flash message --}}
        @if(session('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('message') }}
            </div>
        @endif

        {{-- Courses Grid --}}
        @if($courses->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('lessons.no_courses') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('lessons.no_courses_hint') }}</p>
                <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('lessons.create_course') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition group">
                        {{-- Course Header --}}
                        <div class="p-5 border-b border-gray-100">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-lg text-gray-900 truncate">{{ $course->title }}</h3>
                                    <div class="flex items-center gap-2 mt-2">
                                        @if($course->lesson_lang)
                                            <span class="text-lg" title="{{ $course->lesson_lang->label() }}">
                                                {{ $course->lesson_lang->flag() }}
                                            </span>
                                        @endif
                                        @if($course->language_level)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                {{ $course->language_level->value }}
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $course->is_published ? __('lessons.course_published') : __('lessons.draft') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @if($course->description)
                                <p class="text-sm text-gray-600 mt-3 line-clamp-2">{{ $course->description }}</p>
                            @endif
                        </div>

                        {{-- Course Stats --}}
                        <div class="px-5 py-3 bg-gray-50 flex items-center justify-between">
                            <span class="text-sm text-gray-600">
                                {{ trans_choice('lessons.lessons_in_course', $course->lessons_count, ['count' => $course->lessons_count]) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ __('lessons.passing_score') }}: {{ $course->passing_score }}%</span>
                        </div>

                        {{-- Actions --}}
                        <div class="px-5 py-3 flex items-center gap-2 border-t border-gray-100">
                            <a href="{{ route('courses.show', $course) }}" 
                               class="flex-1 px-3 py-2 text-sm font-medium text-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                {{ __('lessons.view_course') }}
                            </a>
                            <a href="{{ route('courses.edit', $course) }}"
                               class="px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                            <button wire:click="togglePublish({{ $course->id }})"
                                    class="px-3 py-2 text-sm font-medium {{ $course->is_published ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition">
                                @if($course->is_published)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                @endif
                            </button>
                            <button wire:click="confirmDelete({{ $course->id }})"
                                    class="px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($show_delete_confirm)
        <x-confirm-modal-livewire
            icon="danger"
            :title="__('lessons.delete_course')"
            :message="__('lessons.delete_course_confirm')"
            :confirmText="__('lessons.delete')"
            :cancelText="__('lessons.cancel')"
            confirmMethod="deleteCourse"
            cancelMethod="cancelDelete"
        />
    @endif
</div>
