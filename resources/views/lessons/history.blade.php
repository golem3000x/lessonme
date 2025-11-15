<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">

        {{-- Header --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-indigo-700 mb-2">
                        📚 {{ __('lessons.lesson_history') }}
                    </h2>
                    <p class="text-gray-600">{{ $lesson->lesson_name }}</p>
                </div>
                <a href="{{ route('lessons.index') }}" 
                   class="btn btn-outline btn-primary btn-sm">
                    ← {{ __('lessons.my_lessons') }}
                </a>
            </div>
        </div>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- History list --}}
        @if($histories->isEmpty())
            <div class="bg-gray-50 rounded-2xl p-12 text-center border border-gray-200">
                <div class="text-6xl mb-4">📜</div>
                <p class="text-gray-600 text-lg">{{ __('lessons.no_history') }}</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($histories as $history)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
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
                                    <div class="flex gap-4 text-xs text-gray-500">
                                        @php
                                            $grammar_count = is_array($history->grammar_data) ? count($history->grammar_data) : 0;
                                            $vocab_count = is_array($history->vocabulary) ? count($history->vocabulary) : 0;
                                            $quiz_count = is_array($history->quiz) ? count($history->quiz) : 0;
                                        @endphp
                                        
                                        @if($grammar_count > 0)
                                            <span>📝 {{ $grammar_count }} {{ __('lessons.grammar') }}</span>
                                        @endif
                                        @if($vocab_count > 0)
                                            <span>💬 {{ $vocab_count }} {{ __('lessons.vocabulary') }}</span>
                                        @endif
                                        @if($quiz_count > 0)
                                            <span>🎯 {{ $quiz_count }} Quiz</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-4">
                                    <form method="POST" action="{{ route('lessons.history.restore', [$lesson, $history]) }}" 
                                          onsubmit="return confirm('{{ __('lessons.restore_confirm') }}')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            {{ __('lessons.restore_version') }}
                                        </button>
                                    </form>
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
