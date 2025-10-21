<div class="mt-6 p-4 bg-white rounded shadow">
    <h4 class="text-lg font-semibold mb-3">🎯 Vocabulary Quiz</h4>
    @foreach($quiz as $index => $q)
        <div class="mb-4">
            <p class="font-medium mb-2">{{ $q['question'] }}</p>
            <div class="space-y-2">
                @foreach($q['options'] as $opt)
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model="answers.{{ $index }}" value="{{ $opt }}">
                        <span class="px-2 py-1 border rounded">{{ $opt }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(!$checked)
        <button wire:click="checkAnswers" class="bg-blue-600 text-white px-4 py-2 rounded">Check Answers</button>
    @else
        <div class="mt-3 font-semibold">Score: {{ $score }}/{{ count($quiz) }}</div>
    @endif
</div>
