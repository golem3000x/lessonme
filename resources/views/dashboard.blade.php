<x-app-layout>
    <div class="space-y-6">
            @php
                $user = auth()->user();
                $is_teacher = $user->isTeacher();
                $is_student = $user->isStudent();
                $is_both = $is_teacher && $is_student;
            @endphp

            @if($is_both)
                {{-- Tabs for users who are both teacher and student --}}
                <div 
                    x-data="{ activeTab: 'student' }"
                    class="space-y-6"
                >
                    {{-- Tab Buttons --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                        <div class="flex gap-2">
                            <button 
                                @click="activeTab = 'student'"
                                :class="activeTab === 'student' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-medium transition"
                            >
                                <span class="text-xl">📚</span>
                                {{ __('dashboard.my_learning') }}
                            </button>
                            <button 
                                @click="activeTab = 'teacher'"
                                :class="activeTab === 'teacher' ? 'bg-green-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-medium transition"
                            >
                                <span class="text-xl">👨‍🏫</span>
                                {{ __('dashboard.my_teaching') }}
                            </button>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div x-show="activeTab === 'student'" x-transition>
                        <livewire:dashboard.student-dashboard />
                    </div>
                    <div x-show="activeTab === 'teacher'" x-transition>
                        <livewire:dashboard.teacher-dashboard />
                    </div>
                </div>
            @elseif($is_teacher)
                {{-- Only teacher --}}
                <livewire:dashboard.teacher-dashboard />
            @else
                {{-- Only student (default) --}}
                <livewire:dashboard.student-dashboard />
            @endif
    </div>
</x-app-layout>
