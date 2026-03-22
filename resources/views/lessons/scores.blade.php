<x-app-layout>
    <div class="space-y-6 sm:space-y-8">
        {{-- HEADER --}}
        <div class="mb-6">
            <a href="{{ route('lessons.show', $lesson->uuid) }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('lessons.view_lesson') }}
            </a>
        </div>

        {{-- SCORES COMPONENT --}}
        <livewire:teacher.student-scores :lesson_id="$lesson->id" />
    </div>
</x-app-layout>
