<div class="space-y-6">
    {{-- WELCOME HEADER --}}
    @if($show_greeting)
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white relative">
            <button 
                wire:click="hideGreeting"
                class="absolute top-3 right-3 p-1 text-white/70 hover:text-white transition-all duration-300 hover:rotate-90 hover:scale-110"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2 pr-8">👋 {{ $greeting }}, {{ auth()->user()->name }}!</h1>
            <p class="text-blue-100">{{ __('dashboard.student_progress_overview') }}</p>
        </div>
    @endif

    {{-- QUICK TOUR BANNER --}}
    <livewire:help.quick-tour-banner />

    {{-- MAIN STATS GRID --}}
    <div class="stats-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Completed Lessons --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.completed_lessons') }}</h3>
                <span class="text-2xl">📚</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">
                {{ $completed_lessons }} / {{ $total_lessons }}
            </div>
            <div class="mt-2 flex items-center text-sm">
                @if($total_lessons > 0)
                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                        <div class="bg-blue-600 h-2 rounded-full" 
                             style="width: {{ ($completed_lessons / $total_lessons) * 100 }}%"></div>
                    </div>
                    <span class="text-gray-600">{{ round(($completed_lessons / $total_lessons) * 100) }}%</span>
                @else
                    <span class="text-gray-400">{{ __('dashboard.no_lessons_available') }}</span>
                @endif
            </div>
        </div>

        {{-- Average Score --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.average_score') }}</h3>
                <span class="text-2xl">📊</span>
            </div>
            <div class="text-3xl font-bold {{ $average_score >= 80 ? 'text-green-600' : ($average_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $average_score }}%
            </div>
            <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.from_attempts', ['count' => $total_attempts]) }}</p>
        </div>

        {{-- Best Score --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.best_score') }}</h3>
                <span class="text-2xl">🏆</span>
            </div>
            @if($best_score !== null)
                <div class="text-3xl font-bold text-yellow-600">
                    {{ $best_score }}%
                </div>
                <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.personal_record') }}</p>
            @else
                <div class="text-xl text-gray-400 mt-2">
                    {{ __('dashboard.no_scores_yet') }}
                </div>
            @endif
        </div>

        {{-- Latest Score --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.latest_score') }}</h3>
                <span class="text-2xl">⚡</span>
            </div>
            @if($latest_score !== null)
                <div class="text-3xl font-bold text-blue-600">
                    {{ $latest_score }}%
                </div>
                <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.most_recent') }}</p>
            @else
                <div class="text-xl text-gray-400 mt-2">
                    {{ __('dashboard.no_scores_yet') }}
                </div>
            @endif
        </div>
    </div>

    {{-- RECENT SCORES & LEVEL STATS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Scores --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>📝</span> {{ __('dashboard.recent_scores') }}
            </h3>
            
            @if(empty($recent_scores))
                <div class="text-center py-8 text-gray-400">
                    <p>{{ __('dashboard.no_recent_scores') }}</p>
                    <a href="{{ route('lessons.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        {{ __('dashboard.start_learning') }}
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recent_scores as $score)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $score['lesson_name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $score['graded_at'] }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $score['score_percentage'] >= 90 ? 'bg-green-100 text-green-800' : 
                                       ($score['score_percentage'] >= 80 ? 'bg-blue-100 text-blue-800' : 
                                       ($score['score_percentage'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 
                                       ($score['score_percentage'] >= 60 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                                    {{ $score['grade'] }}
                                </span>
                                <span class="text-lg font-bold {{ $score['score_percentage'] >= 80 ? 'text-green-600' : 'text-gray-700' }}">
                                    {{ $score['score_percentage'] }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Level Statistics --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>🎯</span> {{ __('dashboard.level_stats') }}
            </h3>
            
            @if(empty($level_stats))
                <div class="text-center py-8 text-gray-400">
                    <p>{{ __('dashboard.no_stats_yet') }}</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($level_stats as $level => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ $level }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" 
                                         style="width: {{ ($count / max(array_values($level_stats))) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-bold text-gray-900 w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
