<div class="space-y-6">
    {{-- WELCOME HEADER --}}
    @if($show_greeting)
        <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-lg shadow-lg p-6 text-white relative">
            <button 
                wire:click="hideGreeting"
                class="absolute top-3 right-3 p-1 text-white/70 hover:text-white transition-all duration-300 hover:rotate-90 hover:scale-110"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h1 class="text-3xl font-bold mb-2 pr-8">👨‍🏫 {{ $greeting }}, {{ auth()->user()->full_name }}!</h1>
            <p class="text-green-100">{{ __('dashboard.teacher_overview') }}</p>
        </div>
    @endif

    {{-- MAIN STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Students --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.total_students') }}</h3>
                <span class="text-2xl">👥</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $total_students }}</div>
            <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.connected_students') }}</p>
        </div>

        {{-- Lessons Created --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.lessons_created') }}</h3>
                <span class="text-2xl">📝</span>
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ $total_lessons_created }}</div>
            <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.your_lessons') }}</p>
        </div>

        {{-- Lessons Assigned --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.lessons_assigned') }}</h3>
                <span class="text-2xl">📤</span>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $total_lessons_assigned }}</div>
            <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.total_assignments') }}</p>
        </div>

        {{-- Average Student Score --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">{{ __('dashboard.avg_student_score') }}</h3>
                <span class="text-2xl">📊</span>
            </div>
            <div class="text-3xl font-bold {{ $average_student_score >= 80 ? 'text-green-600' : ($average_student_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $average_student_score }}%
            </div>
            <p class="text-sm text-gray-500 mt-2">{{ __('dashboard.across_all_lessons') }}</p>
        </div>
    </div>

    {{-- ASSIGNED LESSONS & STUDENT PERFORMANCE --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Assigned Lessons Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>📋</span> {{ __('dashboard.assigned_lessons') }}
            </h3>
            
            @if(empty($assigned_lessons))
                <div class="text-center py-8 text-gray-400">
                    <p>{{ __('dashboard.no_assignments_yet') }}</p>
                    <a href="{{ route('lessons.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        {{ __('dashboard.create_and_assign') }}
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-2 font-medium text-gray-600">{{ __('dashboard.lesson') }}</th>
                                <th class="text-left py-3 px-2 font-medium text-gray-600">{{ __('dashboard.student') }}</th>
                                <th class="text-center py-3 px-2 font-medium text-gray-600">{{ __('dashboard.status') }}</th>
                                <th class="text-right py-3 px-2 font-medium text-gray-600">{{ __('dashboard.score') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assigned_lessons as $assignment)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-2">
                                        <span class="font-medium text-gray-900 line-clamp-1">{{ $assignment['lesson_name'] }}</span>
                                        <span class="text-xs text-gray-500 block">{{ $assignment['assigned_at'] }}</span>
                                    </td>
                                    <td class="py-3 px-2 text-gray-700">{{ $assignment['student_name'] }}</td>
                                    <td class="py-3 px-2 text-center">
                                        @if($assignment['status'] === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                                ✅ {{ __('dashboard.completed') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">
                                                ⏳ {{ __('dashboard.pending') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        @if($assignment['score'] !== null)
                                            <span class="font-bold {{ $assignment['score'] >= 80 ? 'text-green-600' : ($assignment['score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $assignment['score'] }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Student Performance --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>🎓</span> {{ __('dashboard.student_performance') }}
            </h3>
            
            @if(empty($student_performance))
                <div class="text-center py-8 text-gray-400">
                    <p>{{ __('dashboard.no_students_yet') }}</p>
                    <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        {{ __('dashboard.find_students') }}
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($student_performance as $student)
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            {{-- Avatar --}}
                            @if($student['avatar'])
                                <img src="{{ $student['avatar'] }}" alt="{{ $student['name'] }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($student['name'], 0, 2)) }}
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">{{ $student['name'] }}</h4>
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span>{{ $student['completed'] }}/{{ $student['assigned'] }} {{ __('dashboard.lessons_short') }}</span>
                                    @if($student['last_active'])
                                        <span>{{ $student['last_active'] }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Score --}}
                            <div class="text-right">
                                <span class="text-lg font-bold {{ $student['avg_score'] >= 80 ? 'text-green-600' : ($student['avg_score'] >= 60 ? 'text-yellow-600' : 'text-gray-400') }}">
                                    {{ $student['avg_score'] > 0 ? $student['avg_score'] . '%' : '—' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span>⚡</span> {{ __('dashboard.recent_activity') }}
        </h3>
        
        @if(empty($recent_activity))
            <div class="text-center py-8 text-gray-400">
                <p>{{ __('dashboard.no_activity_yet') }}</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recent_activity as $activity)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-xl">
                            @if($activity['type'] === 'completion')
                                🎉
                            @else
                                📤
                            @endif
                        </span>
                        <div class="flex-1">
                            <p class="text-gray-800">{{ $activity['message'] }}</p>
                            <span class="text-xs text-gray-500">{{ $activity['time'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
