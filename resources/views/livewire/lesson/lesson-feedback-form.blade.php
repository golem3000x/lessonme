<div>
    {{-- FEEDBACK BUTTON --}}
    @if(!$already_submitted)
        <button
            wire:click="openModal"
            class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200"
        >
            <span class="text-2xl">💬</span>
            <span class="text-lg">{{ __('lessons.leave_feedback') }}</span>
        </button>
    @else
        <div class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-gray-100 text-gray-500 font-medium rounded-lg border-2 border-gray-300">
            <span class="text-2xl">✓</span>
            <span class="text-lg">{{ __('lessons.feedback_submitted') }}</span>
        </div>
    @endif

    {{-- FEEDBACK MODAL --}}
    @if($show_modal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
             wire:click.self="closeModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 transform transition-all"
                 @click.stop>
                {{-- HEADER --}}
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">
                        {{ __('lessons.feedback_title') }}
                    </h3>
                    <button
                        wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors"
                    >
                        ×
                    </button>
                </div>

                {{-- RATING BUTTONS --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        {{ __('lessons.how_was_lesson') }}
                    </label>
                    <div class="flex gap-4">
                        {{-- LIKE BUTTON --}}
                        <button
                            wire:click="setRating('like')"
                            type="button"
                            class="flex-1 flex flex-col items-center justify-center gap-3 p-6 rounded-xl border-2 transition-all duration-200
                                   {{ $rating === 'like' ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-white hover:border-green-400 hover:bg-green-50' }}"
                        >
                            <span class="text-5xl">👍</span>
                            <span class="font-medium {{ $rating === 'like' ? 'text-green-700' : 'text-gray-700' }}">
                                {{ __('lessons.like') }}
                            </span>
                        </button>

                        {{-- DISLIKE BUTTON --}}
                        <button
                            wire:click="setRating('dislike')"
                            type="button"
                            class="flex-1 flex flex-col items-center justify-center gap-3 p-6 rounded-xl border-2 transition-all duration-200
                                   {{ $rating === 'dislike' ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white hover:border-red-400 hover:bg-red-50' }}"
                        >
                            <span class="text-5xl">👎</span>
                            <span class="font-medium {{ $rating === 'dislike' ? 'text-red-700' : 'text-gray-700' }}">
                                {{ __('lessons.dislike') }}
                            </span>
                        </button>
                    </div>
                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- COMMENT TEXTAREA --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('lessons.feedback_comment') }} <span class="text-gray-400">({{ __('lessons.optional') }})</span>
                    </label>
                    <textarea
                        wire:model="comment"
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all resize-none"
                        placeholder="{{ __('lessons.feedback_placeholder') }}"
                        maxlength="500"
                    ></textarea>
                    <div class="text-xs text-gray-500 mt-1 text-right">
                        {{ mb_strlen($comment) }}/500
                    </div>
                    @error('comment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex gap-3">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        {{ __('lessons.cancel') }}
                    </button>
                    <button
                        wire:click="submitFeedback"
                        type="button"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ !$rating ? 'disabled' : '' }}
                    >
                        {{ __('lessons.submit_feedback') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
