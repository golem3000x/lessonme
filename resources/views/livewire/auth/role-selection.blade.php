<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-white to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl shadow-xl mb-6">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent mb-3">
                {{ __('auth.role_selection.title') }}
            </h2>
            <p class="text-sm text-gray-600 max-w-sm mx-auto leading-relaxed">
                {{ __('auth.role_selection.description') }}
            </p>
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <form class="space-y-6" wire:submit="completeRegistration">
                
                {{-- Role checkboxes --}}
                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('auth.role_selection.select_roles') }}
                    </label>

                    {{-- Teacher checkbox --}}
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 {{ $is_teacher ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}">
                        <input 
                            type="checkbox" 
                            wire:model.live="is_teacher"
                            class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        >
                        <div class="ml-3">
                            <span class="font-medium text-gray-800">{{ __('auth.role_selection.teacher') }}</span>
                            <p class="text-xs text-gray-500 mt-1">{{ __('auth.role_selection.teacher_description') }}</p>
                        </div>
                    </label>

                    {{-- Teaching languages (shown only if teacher selected) --}}
                    @if($is_teacher)
                        <div class="space-y-2 animate-fadeIn">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('auth.role_selection.teaching_languages') }}
                            </label>
                            <div x-data="{ open: false }" class="relative">
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 bg-white flex items-center justify-between hover:border-green-400 transition focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                >
                                    <span class="text-sm text-gray-700">{{ __('auth.role_selection.select_languages') }}</span>
                                    <div class="flex items-center gap-2">
                                        @if(count($teaching_languages) > 0)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                                {{ count($teaching_languages) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">0</span>
                                        @endif
                                        <svg class="h-4 w-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                                <div 
                                    x-cloak 
                                    x-show="open" 
                                    @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 right-0 z-50 mt-1 rounded-lg border border-gray-200 bg-white shadow-lg"
                                >
                                    <div class="p-2 space-y-1">
                                        @foreach($languages as $language)
                                            <label class="flex items-center space-x-3 p-2 rounded-lg cursor-pointer hover:bg-green-50 transition">
                                                <input 
                                                    type="checkbox" 
                                                    value="{{ $language->value }}"
                                                    wire:model.live="teaching_languages"
                                                    class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                                >
                                                <span class="text-sm text-gray-800">{{ $language->flag() }} {{ $language->label() }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            @error('teaching_languages')
                                <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                                    <p class="text-sm text-red-700">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    @endif

                    {{-- Student checkbox --}}
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 {{ $is_student ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}">
                        <input 
                            type="checkbox" 
                            wire:model.live="is_student"
                            class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        >
                        <div class="ml-3">
                            <span class="font-medium text-gray-800">{{ __('auth.role_selection.student') }}</span>
                            <p class="text-xs text-gray-500 mt-1">{{ __('auth.role_selection.student_description') }}</p>
                        </div>
                    </label>

                    @error('roles')
                        <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                            <p class="text-sm text-red-700 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        </div>
                    @enderror
                </div>

                {{-- Learning languages (shown only if student selected) --}}
                @if($is_student)
                    <div class="space-y-2 animate-fadeIn">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ __('auth.role_selection.learning_languages') }}
                        </label>
                        <div x-data="{ open: false }" class="relative">
                            <button 
                                type="button" 
                                @click="open = !open" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 bg-white flex items-center justify-between hover:border-green-400 transition focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                                <span class="text-sm text-gray-700">{{ __('auth.role_selection.select_languages') }}</span>
                                <div class="flex items-center gap-2">
                                    @if(count($learning_languages) > 0)
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                            {{ count($learning_languages) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">0</span>
                                    @endif
                                    <svg class="h-4 w-4 text-gray-500 transition-transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                            <div 
                                x-cloak 
                                x-show="open" 
                                @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 right-0 z-50 mt-1 rounded-lg border border-gray-200 bg-white shadow-lg"
                            >
                                <div class="p-2 space-y-1">
                                    @foreach($languages as $language)
                                        <label class="flex items-center space-x-3 p-2 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                            <input 
                                                type="checkbox" 
                                                value="{{ $language->value }}"
                                                wire:model.live="learning_languages"
                                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            >
                                            <span class="text-sm text-gray-800">{{ $language->flag() }} {{ $language->label() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        @error('learning_languages')
                            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                                <p class="text-sm text-red-700">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>
                @endif

                <button 
                    type="submit"
                    class="group w-full flex items-center justify-center gap-2 py-3 px-4 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md hover:shadow-lg transition-all duration-200"
                >
                    <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('auth.role_selection.submit') }}
                </button>

                {{-- Info card --}}
                <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-teal-50 border border-green-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-gray-700 leading-relaxed">
                            {{ __('auth.role_selection.info_text') }}
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
