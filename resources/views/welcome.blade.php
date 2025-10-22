<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('lessons.welcome') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow rounded-lg">
                <p class="text-gray-600">{{ __('lessons.welcome_to_app') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>

