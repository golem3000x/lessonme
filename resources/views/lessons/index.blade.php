<x-app-layout>
    <div class="p-6 bg-white rounded shadow">
        <h2 class="text-xl font-semibold mb-4">{{ __('lessons.my_lessons') }}</h2>
        @if($lessons->isEmpty())
            <p class="text-gray-500">{{ __('lessons.no_lessons_yet') }} {{ __('lessons.create_from_chat') }}</p>
        @else
            <ul>
                @foreach($lessons as $lesson)
                    <li class="mb-2">
                        <a href="{{ route('lessons.show', $lesson->id) }}"
                           class="text-blue-600">{{ $lesson->lesson_name ?? Str::limit($lesson->lesson_text,50).'...' }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
