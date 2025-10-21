<x-app-layout>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Generate Lesson</h2>
        <form method="POST" action="{{ route('lessons.store') }}">
            @csrf
            <div class="mb-3">
                <label>Topic</label>
                <input type="text" name="topic" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label>Level</label>
                <select name="level" class="w-full border rounded p-2">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Themes (optional)</label>
                <input type="text" name="themes[]" class="w-full border rounded p-2" placeholder="Programming, Cooking">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
        </form>
    </div>
</x-app-layout>
