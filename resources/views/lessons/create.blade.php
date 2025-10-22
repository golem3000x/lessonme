<x-app-layout>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">{{ __('lessons.generate_lesson') }}</h2>
        <form method="POST" action="{{ route('lessons.store') }}">
            @csrf
            <div class="mb-3">
                <label>{{ __('lessons.topic') }}</label>
                <input type="text" name="topic" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label>{{ __('lessons.level') }}</label>
                <select name="level" class="w-full border rounded p-2">
                    <option value="beginner">{{ __('lessons.beginner') }}</option>
                    <option value="intermediate">{{ __('lessons.intermediate') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label>{{ __('lessons.themes_optional') }}</label>
                <input type="text" name="themes[]" class="w-full border rounded p-2" placeholder="{{ __('lessons.themes_placeholder') }}">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">{{ __('lessons.generate') }}</button>
        </form>
    </div>
</x-app-layout>
