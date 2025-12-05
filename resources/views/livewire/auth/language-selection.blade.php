<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-white to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl shadow-xl mb-6">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent mb-3">
                {{ __('auth.language_selection.title') }}
            </h2>
            <p class="text-sm text-gray-600 max-w-sm mx-auto leading-relaxed">
                {{ __('auth.language_selection.description') }}
            </p>
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <form class="space-y-6" wire:submit="proceedToRoleSelection">
                <div>
                    <label for="native_lang" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('auth.language_selection.native_lang_label') }}
                    </label>
                    
                    <select 
                        id="native_lang" 
                        wire:model="native_lang"
                        class="w-full px-4 py-3 text-base border-2 border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                        required
                    >
                        <option value="">{{ __('auth.language_selection.select_language') }}</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->value }}">
                                {{ $language->flag() }} {{ $language->label() }}
                            </option>
                        @endforeach
                    </select>
                    
                    @error('native_lang')
                        <div class="mt-2 p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                            <p class="text-sm text-red-700 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        </div>
                    @enderror
                </div>

                <button 
                    type="submit"
                    class="group w-full flex items-center justify-center gap-2 py-3 px-4 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md hover:shadow-lg transition-all duration-200"
                >
                    <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('auth.language_selection.submit') }}
                </button>

                {{-- Info card --}}
                <div class="mt-6 p-4 bg-gradient-to-r from-green-50 to-teal-50 border border-green-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-gray-700 leading-relaxed">
                            {{ __('auth.language_selection.info_text') }}
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
