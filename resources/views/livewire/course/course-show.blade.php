<div class="max-w-4xl space-y-6 sm:space-y-8">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('lessons.my_courses') }}
            </a>
            
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
                    @if($course->description)
                        <p class="text-gray-600 mt-2">{{ $course->description }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-3">
                        @if($course->lesson_lang)
                            <span class="text-xl" title="{{ $course->lesson_lang->label() }}">{{ $course->lesson_lang->flag() }}</span>
                        @endif
                        @if($course->language_level)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $course->language_level->value }}
                            </span>
                        @endif
                    </div>
                </div>
                
                @if(auth()->user()->id === $course->user_id)
                    <a href="{{ route('courses.edit', $course) }}" 
                       class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition font-medium">
                        {{ __('lessons.edit_course') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-3">
                <span class="font-semibold text-gray-900">{{ __('lessons.course_progress') }}</span>
                <span class="text-sm text-gray-600">{{ $completed_count }}/{{ $total_count }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500"
                     style="width: {{ $progress_percentage }}%"></div>
            </div>
            @if($progress_percentage === 100)
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-green-800">{{ __('lessons.course_completed') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Lessons List --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">{{ __('lessons.course_lessons') }}</h2>
            </div>
            
            <div class="divide-y divide-gray-100">
                @foreach($lessons_with_progress as $index => $lesson)
                    <div class="flex items-center gap-4 px-6 py-4 {{ $lesson['is_locked'] ? 'bg-gray-50 opacity-60' : '' }}">
                        {{-- Status Icon --}}
                        <div class="flex-shrink-0">
                            @if($lesson['is_passed'])
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @elseif($lesson['is_locked'])
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="font-semibold text-blue-600">{{ $lesson['order'] }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Lesson Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900">{{ $lesson['name'] }}</h3>
                            @if($lesson['is_passed'] && $lesson['score'] !== null)
                                <p class="text-sm text-green-600">{{ __('lessons.score') }}: {{ $lesson['score'] }}%</p>
                            @elseif($lesson['is_locked'])
                                <p class="text-sm text-gray-500">{{ __('lessons.lesson_locked') }}</p>
                            @endif
                        </div>

                        {{-- Action --}}
                        @if(!$lesson['is_locked'])
                            <a href="{{ route('lessons.show', $lesson['uuid']) }}" 
                               class="px-4 py-2 text-sm font-medium {{ $lesson['is_passed'] ? 'text-gray-600 border border-gray-300 hover:bg-gray-50' : 'text-white bg-blue-600 hover:bg-blue-700' }} rounded-lg transition">
                                {{ $lesson['is_passed'] ? __('lessons.view_lesson') : __('lessons.start_lesson') }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
</div>
