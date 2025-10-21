<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Profile setup</h2>
    <div class="mb-3">
        <label class="block">Name</label>
        <input type="text" wire:model="name" class="w-full border rounded p-2" />
    </div>
    <div class="mb-3">
        <label class="block">Native language</label>
        <select wire:model="native_lang" class="w-full border rounded p-2">
            <option value="vi">Vietnamese (vi)</option>
            <option value="en">English (en)</option>
            <option value="ru">Russian (ru)</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="block">Learning language</label>
        <select wire:model="learning_lang" class="w-full border rounded p-2">
            <option value="en">English (en)</option>
            <option value="vi">Vietnamese (vi)</option>
        </select>
    </div>
    <div class="flex justify-end">
        <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
    </div>
</div>
