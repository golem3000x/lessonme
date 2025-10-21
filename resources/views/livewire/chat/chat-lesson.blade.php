<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">AI Practice — {{ $topic }}</h3>
        <div>
            <input type="text" wire:model="topic" class="border rounded px-2 py-1" />
            <button wire:click="startLesson" class="ml-2 bg-green-600 text-white px-3 py-1 rounded">Start</button>
        </div>
    </div>

    <div class="h-72 overflow-y-auto border p-4 mb-4 bg-gray-50">
        @foreach($messages as $m)
            <div class="mb-3 {{ $m['sender'] === 'user' ? 'text-right' : 'text-left' }}">
                <span class="inline-block px-3 py-2 rounded-xl {{ $m['sender'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
                    {{ $m['content'] }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="flex">
        <input wire:model="input" wire:keydown.enter="send" class="flex-grow border rounded px-3 py-2" placeholder="Type your reply..." />
        <button wire:click="send" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded">Send</button>
    </div>
</div>
