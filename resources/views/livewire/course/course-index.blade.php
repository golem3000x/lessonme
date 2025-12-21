<div class="max-w-6xl mx-auto p-6 space-y-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                {{ __('lessons.courses') }}
            </h2>
        </div>

        {{-- View Mode Tabs --}}
        <div class="flex items-center justify-between">
            <div class="flex gap-1 p-1 bg-gray-100 rounded-lg">
                @if($is_teacher)
                    <button
                        wire:click="setViewMode('my_courses')"
                        class="px-4 py-2 text-sm font-medium rounded-md transition {{ $view_mode === 'my_courses' ? 'bg-white text-purple-600 shadow' : 'text-gray-600 hover:text-gray-900' }}"
                    >
                        {{ __('lessons.my_courses') }}
                    </button>
                @endif
                <button
                    wire:click="setViewMode('assigned')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $view_mode === 'assigned' || !$is_teacher ? 'bg-white text-purple-600 shadow' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    {{ __('lessons.assigned_to_me') }}
                </button>
            </div>

            @if($is_teacher && $view_mode === 'my_courses')
                <a href="{{ route('courses.create') }}" 
                   class="group flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 shadow transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('lessons.create_course') }}</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Teacher role notice for non-teachers --}}
    @if(!$is_teacher)
        <div class="flex items-center gap-2 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg">
            <svg class="w-5 h-5 drop-shadow-sm flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <span class="font-medium">{{ __('lessons.teacher_role_required') }}:</span>
                <span class="text-blue-700">{{ __('lessons.teacher_role_hint') }}</span>
            </div>
        </div>
    @endif

    {{-- Flash messages --}}
    @if(session('message'))
        <div class="flex items-center gap-2 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg">
            <svg class="w-5 h-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Empty state --}}
    @if($courses->isEmpty())
        <div class="text-center py-16 space-y-4">
            <svg class="mx-auto h-14 w-14 text-gray-400 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            @if($view_mode === 'my_courses' && $is_teacher)
                <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_courses') }}</h3>
                <p class="text-sm text-gray-500">{{ __('lessons.no_courses_hint') }}</p>
            @else
                <h3 class="text-xl font-medium text-gray-900">{{ __('lessons.no_assigned_courses') }}</h3>
                <p class="text-sm text-gray-500">{{ __('lessons.no_assigned_courses_hint') }}</p>
            @endif
        </div>
    @else
        {{-- Courses Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            @foreach($courses as $course)
                <div class="group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:-translate-y-1 transition p-5 relative flex flex-col h-full">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50/20 to-transparent opacity-0 group-hover:opacity-100 transition pointer-events-none"></div>
                    
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-800 line-clamp-2 mb-2 min-h-[3.5rem]">{{ $course->title }}</h3>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                @if($course->lesson_lang)
                                    <span class="text-xl" title="{{ $course->lesson_lang->label() }}">
                                        {{ $course->lesson_lang->flag() }}
                                    </span>
                                @endif
                                
                                @if($course->language_level)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $course->language_level->value }}
                                    </span>
                                @endif

                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 drop-shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    {{ trans_choice('lessons.lessons_in_course', $course->lessons_count, ['count' => $course->lessons_count]) }}
                                </span>
                            </div>
                        </div>

                        @if($view_mode === 'my_courses' && $is_teacher)
                            {{-- Dropdown menu - only for own courses --}}
                            <div class="relative ml-2" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="group p-2 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-purple-50 transition-all duration-200"
                                        :class="{ 'bg-purple-50 text-purple-600': open }">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden">
                                    <a href="{{ route('courses.edit', $course) }}"
                                       class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-all">
                                        <div class="p-1.5 rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </div>
                                        {{ __('lessons.edit_course') }}
                                    </a>

                                    <button wire:click="openAssignModal({{ $course->id }})"
                                            @click="open = false"
                                            class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-blue-600 hover:bg-blue-50 transition-all">
                                        <div class="p-1.5 rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                        </div>
                                        {{ __('lessons.assign_to_students') }}
                                    </button>

                                    <div class="border-t border-gray-100"></div>
                                    
                                    <button wire:click="confirmDelete({{ $course->id }})"
                                            class="group flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all">
                                        <div class="p-1.5 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </div>
                                        {{ __('lessons.delete_course') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($course->description)
                        <p class="text-sm text-gray-600 mb-4 leading-snug line-clamp-3 flex-grow">{{ $course->description }}</p>
                    @else
                        <div class="flex-grow"></div>
                    @endif

                    @if(!$is_teacher && $course->user)
                        <div class="flex items-center gap-2 mb-4 pt-3 border-t border-gray-100">
                            @if($course->user->avatar)
                                <img src="{{ $course->user->avatar_url }}" alt="{{ $course->user->name }}" class="w-6 h-6 rounded-full">
                            @else
                                <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-purple-600">{{ substr($course->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="text-xs text-gray-500">{{ __('lessons.by_author', ['name' => $course->user->name]) }}</span>
                        </div>
                    @endif

                    {{-- Action Button --}}
                    <a href="{{ route('courses.show', $course) }}" 
                       class="group inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-purple-600 text-white rounded-md font-medium hover:bg-purple-700 transition mt-auto">
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M14 5l7 7-7 7M3 12h18"/>
                        </svg>
                        {{ __('lessons.view_course') }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

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

    {{-- Assign Course Modal --}}
    @if($show_assign_modal)
        <div
            x-data
            x-init="document.body.style.overflow = 'hidden'"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="$wire.closeAssignModal()"
        >
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" x-transition></div>

            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[80vh] overflow-hidden"
                    x-transition
                    @click.stop
                >
                    {{-- Header --}}
                    <div class="p-5 bg-gradient-to-r from-purple-600 to-indigo-600 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">{{ __('lessons.assign_course_to_students') }}</h3>
                        </div>
                        <button wire:click="closeAssignModal" class="p-1.5 rounded-lg bg-white/20 hover:bg-white/30 transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 overflow-y-auto max-h-[50vh]">
                        @if($this->myStudents->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="mt-2 text-gray-600">{{ __('lessons.no_students_yet') }}</p>
                                <a href="{{ route('users.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">
                                    {{ __('lessons.add_students_hint') }}
                                </a>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($this->myStudents as $student)
                                    @php
                                        $is_assigned = in_array($student->id, $this->assignedStudentIds);
                                    @endphp

                                    <div class="flex items-center gap-3 p-3 rounded-lg border {{ $is_assigned ? 'border-purple-300 bg-purple-50' : 'border-gray-200' }}">
                                        <div class="flex-shrink-0">
                                            @if($student->avatar)
                                                <img src="{{ $student->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $student->name }}</p>
                                            <p class="text-sm text-gray-500 truncate">{{ $student->email }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($is_assigned)
                                                <button
                                                    wire:click="unassignStudent({{ $student->id }})"
                                                    class="px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition"
                                                >
                                                    {{ __('lessons.unassign') }}
                                                </button>
                                            @else
                                                <button
                                                    wire:click="assignSingleStudent({{ $student->id }})"
                                                    class="px-3 py-1.5 text-sm font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition"
                                                >
                                                    {{ __('lessons.assign') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
