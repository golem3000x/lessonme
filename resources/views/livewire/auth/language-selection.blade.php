<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center mb-4">
                <svg class="h-16 w-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
            </div>
            <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
                {{ __('auth.language_selection.title') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('auth.language_selection.description') }}
            </p>
        </div>

        <form class="mt-8 space-y-6" wire:submit="completeRegistration">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="native_lang" class="block text-sm font-medium text-gray-700">
                        {{ __('auth.language_selection.native_lang_label') }}
                    </label>
                    <select 
                        id="native_lang" 
                        wire:model="native_lang"
                        class="mt-1 block w-full px-3 py-2.5 text-base border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button 
                    type="submit"
                    class="group relative w-full flex items-center justify-center gap-2 py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <svg class="h-5 w-5 drop-shadow-sm transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('auth.language_selection.submit') }}
                </button>
            </div>
        </form>
    </div>
</div>
