<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-6">

        {{-- Header --}}
        <div class="bg-gradient-to-br from-white to-gray-50 p-8 rounded-2xl shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-md">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent mb-1">
                            {{ __('lessons.lesson_history') }}
                        </h2>
                        <p class="text-gray-600 font-medium">{{ $lesson->lesson_name }}</p>
                    </div>
                </div>
                <a href="{{ route('lessons.index') }}" 
                   class="group inline-flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 font-medium transition-all duration-200 shadow-sm">
                    <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('lessons.my_lessons') }}
                </a>
            </div>
        </div>

        {{-- Success message --}}
        @if(session('success'))
            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- History list --}}
        @if($histories->isEmpty())
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-700">{{ __('lessons.no_history') }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ __('lessons.no_history_description') }}</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($histories as $history)
                    <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-blue-200 transition-all duration-200 relative"
                         x-data="{ isRestoring_{{ $history->id }}: false }">
                        
                        {{-- Loading overlay --}}
                        <div x-show="isRestoring_{{ $history->id }}" 
                             x-cloak
                             class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
                            <div class="text-center">
                                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-3"></div>
                                <p class="text-sm font-semibold text-gray-700">{{ __('lessons.restoring') }}...</p>
                            </div>
                        </div>

                        <div class="p-6 bg-gradient-to-r from-white to-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                        {{ $history->lesson_name }}
                                    </h3>
                                    
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4">
                                        @if($history->language_level)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $history->level_badge_color }}">
                                                {{ $history->language_level->value }}
                                            </span>
                                        @endif
                                        
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('lessons.version_created') }}: {{ $history->created_at->format('d M Y, H:i') }}
                                        </span>
                                        
                                        @if($history->topic_id)
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-medium">
                                                {{ __('lessons.topic') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Preview of lesson text --}}
                                    @if(!empty($history->lesson_text) && is_string($history->lesson_text))
                                        <div class="text-gray-600 text-sm mb-4 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            {{ Str::limit($history->lesson_text, 200) }}
                                        </div>
                                    @endif

                                    {{-- Statistics --}}
                                    <div class="flex flex-wrap gap-2">
                                        @php
                                            $grammar_count = is_array($history->grammar_data) ? count($history->grammar_data) : 0;
                                            $vocab_count = is_array($history->vocabulary) ? count($history->vocabulary) : 0;
                                            $dialogue_count = is_array($history->dialogue) ? count($history->dialogue) : 0;
                                            $exercises_count = is_array($history->exercises) ? array_sum(array_map('count', $history->exercises)) : 0;
                                            $has_homework = !empty($history->homework);
                                            $mistakes_count = is_array($history->common_mistakes) ? count($history->common_mistakes) : 0;
                                            $has_culture = !empty($history->culture_notes);
                                            $quiz_count = is_array($history->quiz) ? count($history->quiz) : 0;
                                        @endphp
                                        
                                        @if($grammar_count > 0)
                                            <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium border border-blue-200">
                                                📝 {{ $grammar_count }} {{ __('lessons.grammar') }}
                                            </span>
                                        @endif
                                        @if($vocab_count > 0)
                                            <span class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-medium border border-purple-200">
                                                💬 {{ $vocab_count }} {{ __('lessons.vocabulary') }}
                                            </span>
                                        @endif
                                        @if($dialogue_count > 0)
                                            <span class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-medium border border-green-200">
                                                💬 {{ $dialogue_count }} Dialogue
                                            </span>
                                        @endif
                                        @if($exercises_count > 0)
                                            <span class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg text-xs font-medium border border-orange-200">
                                                📝 {{ $exercises_count }} Exercises
                                            </span>
                                        @endif
                                        @if($has_homework)
                                            <span class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium border border-indigo-200">
                                                📚 Homework
                                            </span>
                                        @endif
                                        @if($mistakes_count > 0)
                                            <span class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-medium border border-red-200">
                                                ⚠️ {{ $mistakes_count }} Mistakes
                                            </span>
                                        @endif
                                        @if($has_culture)
                                            <span class="px-3 py-1.5 bg-teal-50 text-teal-700 rounded-lg text-xs font-medium border border-teal-200">
                                                🌍 Culture
                                            </span>
                                        @endif
                                        @if($quiz_count > 0)
                                            <span class="px-3 py-1.5 bg-pink-50 text-pink-700 rounded-lg text-xs font-medium border border-pink-200">
                                                🎯 {{ $quiz_count }} Quiz
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-4 flex-shrink-0" x-data="{ showRestoreModal_{{ $history->id }}: false }">
                                    <button 
                                        @click="showRestoreModal_{{ $history->id }} = true"
                                        :disabled="isRestoring_{{ $history->id }}"
                                        class="group inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-medium shadow-md hover:shadow-lg transition-all duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        {{ __('lessons.restore_version') }}
                                    </button>

                                    {{-- Custom modal for restore with loading state --}}
                                    <div
                                        x-show="showRestoreModal_{{ $history->id }}"
                                        x-cloak
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        @click.self="showRestoreModal_{{ $history->id }} = false"
                                    >
                                        {{-- Backdrop --}}
                                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                                             x-show="showRestoreModal_{{ $history->id }}"
                                             x-transition
                                        ></div>

                                        {{-- Modal --}}
                                        <div class="flex items-center justify-center min-h-screen p-4">
                                            <div
                                                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100"
                                                x-show="showRestoreModal_{{ $history->id }}"
                                                x-transition
                                                @click.stop
                                            >
                                                {{-- Icon --}}
                                                <div class="flex justify-center pt-8 pb-4">
                                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full">
                                                        <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                {{-- Content --}}
                                                <div class="px-6 pb-6 text-center">
                                                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                                                        {{ __('lessons.restore_version') }}?
                                                    </h3>
                                                    <p class="text-sm text-gray-600 mb-6">
                                                        {{ __('lessons.restore_confirm') }}
                                                    </p>

                                                    {{-- Buttons --}}
                                                    <div class="flex gap-3">
                                                        <button
                                                            @click="showRestoreModal_{{ $history->id }} = false"
                                                            type="button"
                                                            class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200"
                                                        >
                                                            {{ __('lessons.cancel') }}
                                                        </button>
                                                        
                                                        <form method="POST" 
                                                              action="{{ route('lessons.history.restore', [$lesson, $history]) }}" 
                                                              class="flex-1"
                                                              @submit="isRestoring_{{ $history->id }} = true; showRestoreModal_{{ $history->id }} = false">
                                                            @csrf
                                                            @method('POST')
                                                            <button
                                                                type="submit"
                                                                class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-600 shadow-md hover:shadow-lg transition-all duration-200"
                                                            >
                                                                {{ __('lessons.restore_version') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $histories->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
