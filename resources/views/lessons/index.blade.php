<x-app-layout>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">My Lessons</h2>
        @if($lessons->isEmpty())
            <p class="text-gray-500">No lessons yet. Create one from Chat.</p>
        @else
            <ul>
                @foreach($lessons as $lesson)
                    <li class="mb-2">
                        <a href="{{ route('lessons.show', $lesson->id) }}" class="text-blue-600">{{ $lesson->topic }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
