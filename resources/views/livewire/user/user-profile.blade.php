<div class="max-w-4xl mx-auto">
    {{-- Profile Header --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        {{-- Cover gradient --}}
        <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        
        {{-- Profile info --}}
        <div class="px-6 pb-6">
            {{-- Avatar --}}
            <div class="relative -mt-16 mb-4">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" 
                         alt="{{ $user->full_name }}"
                         class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                @else
                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-4xl font-bold text-white">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Name and roles --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                    <div class="flex items-center gap-2 mt-2">
                        @if($is_teacher)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                                {{ __('profile.teacher') }}
                            </span>
                        @endif
                        @if($is_student)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ __('profile.student') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center gap-3">
                    @if($is_own_profile)
                        <a href="{{ route('onboarding.profile') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('profile.edit_profile') }}
                        </a>
                    @else
                        <a href="{{ route('messages.chat', $user) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            {{ __('profile.send_message') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Details --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        {{-- Left column: About --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Bio --}}
            @if($user->bio)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('profile.about') }}
                </h2>
                <p class="text-gray-700 leading-relaxed">{{ $user->bio }}</p>
            </div>
            @endif

            {{-- Languages --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('profile.languages') }}
                </h2>
                
                <div class="space-y-4">
                    {{-- Native language --}}
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ \App\Enums\System\SystemLanguageEnum::tryFrom($user->native_lang)?->flag() ?? '🌐' }}</span>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('profile.native_language') }}</p>
                            <p class="font-medium text-gray-900">{{ \App\Enums\System\SystemLanguageEnum::tryFrom($user->native_lang)?->label() ?? $user->native_lang }}</p>
                        </div>
                    </div>

                    {{-- Learning languages (for students) --}}
                    @if($is_student && count($learning_languages) > 0)
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">📖</span>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('profile.learning') }}</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($learning_languages as $lang)
                                    @php($lang_enum = \App\Enums\System\SystemLanguageEnum::tryFrom($lang))
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-sm rounded-lg">
                                        {{ $lang_enum?->flag() }} {{ $lang_enum?->label() ?? $lang }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Teaching languages (for teachers) --}}
                    @if($is_teacher && count($teaching_languages) > 0)
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">📚</span>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('profile.teaching') }}</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($teaching_languages as $lang)
                                    @php($lang_enum = \App\Enums\System\SystemLanguageEnum::tryFrom($lang))
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 text-sm rounded-lg">
                                        {{ $lang_enum?->flag() }} {{ $lang_enum?->label() ?? $lang }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column: Status & Stats --}}
        <div class="space-y-6">
            {{-- Availability status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('profile.status') }}</h2>
                
                @if($is_teacher && $user->is_accepting_students)
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-green-700">{{ __('profile.accepting_students') }}</span>
                    </div>
                @elseif($is_teacher)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-600">{{ __('profile.not_accepting_students') }}</span>
                    </div>
                @endif

                @if($is_student && $user->is_looking_for_teacher)
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg mt-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-blue-700">{{ __('profile.looking_for_teacher') }}</span>
                    </div>
                @elseif($is_student)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mt-3">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-600">{{ __('profile.not_looking_for_teacher') }}</span>
                    </div>
                @endif
            </div>

            {{-- Rating Summary (for teachers) --}}
            @if($is_teacher)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ __('profile.rating') }}</h2>
                @if($user->average_rating)
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= $user->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $user->average_rating }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ __('profile.based_on_reviews', ['count' => $user->reviews_count]) }}</p>
                @else
                    <p class="text-gray-500">{{ __('profile.no_reviews_yet') }}</p>
                @endif

                {{-- Review button --}}
                @if($can_review)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <button wire:click="toggleReviewForm"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 {{ $existing_review ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-yellow-500 text-white hover:bg-yellow-600' }} text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $existing_review ? __('profile.edit_review') : __('profile.write_review') }}
                        </button>
                    </div>
                @endif
            </div>
            @endif

            {{-- Member since --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ __('profile.member_info') }}</h2>
                <div class="flex items-center gap-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm">{{ __('profile.member_since', ['date' => $user->created_at->format('M Y')]) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Form Modal --}}
    @if($show_review_form && $can_review)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">
                {{ $existing_review ? __('profile.edit_review') : __('profile.write_review') }}
            </h3>

            {{-- Rating stars --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.your_rating') }}</label>
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" 
                                wire:click="$set('review_rating', {{ $i }})"
                                class="focus:outline-none transition-transform hover:scale-110">
                            <svg class="w-8 h-8 {{ $i <= $review_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    @endfor
                </div>
            </div>

            {{-- Comment --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('profile.your_comment') }}</label>
                <textarea wire:model="review_comment"
                          rows="3"
                          maxlength="500"
                          placeholder="{{ __('profile.review_placeholder') }}"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                <p class="text-xs text-gray-500 mt-1">{{ strlen($review_comment) }}/500</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button wire:click="submitReview"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    {{ __('profile.submit_review') }}
                </button>
                <button wire:click="toggleReviewForm"
                        class="px-4 py-2 text-gray-600 hover:text-gray-900 font-medium rounded-lg hover:bg-gray-100 transition">
                    {{ __('lessons.cancel') }}
                </button>
            </div>

            {{-- Delete review --}}
            @if($existing_review)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button wire:click="deleteReview"
                            wire:confirm="{{ __('profile.confirm_delete_review') }}"
                            class="w-full text-sm text-red-600 hover:text-red-700">
                        {{ __('profile.delete_review') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Reviews Section (for teachers) --}}
    @if($is_teacher && $reviews->count() > 0)
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            {{ __('profile.reviews') }}
        </h2>

        @if(session('review_message'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                {{ session('review_message') }}
            </div>
        @endif

        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex items-start gap-3">
                        @if($review->student->avatar)
                            <img src="{{ $review->student->avatar_url }}" 
                                 alt="{{ $review->student->full_name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ strtoupper(substr($review->student->first_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900">{{ $review->student->full_name }}</span>
                                <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            @if($review->comment)
                                <p class="mt-2 text-sm text-gray-600">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
