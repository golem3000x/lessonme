# Modal Components - Documentation

Two reusable components for confirmation modal windows.

---

## 1. `<x-confirm-modal>` - For Regular Forms

Used for modals with regular HTML forms (POST, DELETE requests).

### Parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `icon` | string | `'warning'` | Icon type: `warning`, `danger`, `success`, `info` |
| `title` | string | *required* | Modal title |
| `message` | string | *required* | Message text |
| `confirmText` | string | `'Confirm'` | Confirm button text |
| `cancelText` | string | `'Cancel'` | Cancel button text |
| `confirmRoute` | string | `null` | Route for form submission |
| `confirmMethod` | string | `'POST'` | HTTP method (POST, DELETE, PUT, etc.) |
| `showVariable` | string | `'showModal'` | Alpine.js variable name for control |

### Usage Example:

```blade
<div x-data="{ showDeleteModal: false }">
    {{-- Button to open modal --}}
    <button @click="showDeleteModal = true">
        Delete
    </button>

    {{-- Modal window --}}
    <x-confirm-modal
        icon="danger"
        :title="__('Delete Item?')"
        :message="__('This action cannot be undone.')"
        :confirmText="__('Delete')"
        :cancelText="__('Cancel')"
        :confirmRoute="route('items.destroy', $item)"
        confirmMethod="DELETE"
        showVariable="showDeleteModal"
    />
</div>
```

### Icon Types:

#### `icon="danger"` or `icon="delete"`
- Red delete icon
- Used for destructive actions

#### `icon="warning"` or `icon="regenerate"` (default)
- Yellow/orange warning icon
- Used for warning actions

#### `icon="success"` or `icon="restore"`
- Green success icon
- Used for restore or positive actions

#### `icon="info"`
- Blue information icon
- Used for informational messages

---

## 2. `<x-confirm-modal-livewire>` - For Livewire

Used for modals inside Livewire components.

### Parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `icon` | string | `'warning'` | Icon type (same as regular modal) |
| `title` | string | *required* | Modal title |
| `message` | string | *required* | Message text |
| `confirmText` | string | `'Confirm'` | Confirm button text |
| `cancelText` | string | `'Cancel'` | Cancel button text |
| `confirmMethod` | string | `null` | Livewire method name for confirmation |
| `cancelMethod` | string | `null` | Livewire method name for cancel |

### Usage Example in Livewire:

```blade
{{-- In Livewire component --}}
@if($show_delete_confirm)
    <x-confirm-modal-livewire
        icon="danger"
        :title="__('lessons.delete_lesson_title')"
        :message="__('lessons.delete_lesson_message')"
        :confirmText="__('lessons.delete')"
        :cancelText="__('lessons.cancel')"
        confirmMethod="deleteLesson"
        cancelMethod="cancelDelete"
    />
@endif
```

### In Livewire PHP Class:

```php
class MyComponent extends Component
{
    public bool $show_delete_confirm = false;
    public ?int $item_to_delete = null;

    public function confirmDelete(int $id): void
    {
        $this->item_to_delete = $id;
        $this->show_delete_confirm = true;
    }

    public function cancelDelete(): void
    {
        $this->show_delete_confirm = false;
        $this->item_to_delete = null;
    }

    public function deleteLesson(): void
    {
        // Delete logic
        $this->show_delete_confirm = false;
    }
}
```

---

## Real Usage Examples:

### 1. Delete Lesson (Livewire)
```blade
<x-confirm-modal-livewire
    icon="danger"
    :title="__('lessons.delete_lesson_title')"
    :message="__('lessons.delete_lesson_message')"
    :confirmText="__('lessons.delete')"
    :cancelText="__('lessons.cancel')"
    confirmMethod="deleteLesson"
    cancelMethod="cancelDelete"
/>
```

### 2. Regenerate Lesson (Livewire)
```blade
<x-confirm-modal-livewire
    icon="warning"
    :title="__('lessons.regenerate_lesson_title')"
    :message="__('lessons.regenerate_lesson_message')"
    :confirmText="__('lessons.regenerate')"
    :cancelText="__('lessons.cancel')"
    confirmMethod="regenerateLesson"
    cancelMethod="cancelRegenerate"
/>
```

### 3. Restore Version (Regular Form)
```blade
<div x-data="{ showRestoreModal: false }">
    <button @click="showRestoreModal = true">Restore</button>
    
    <x-confirm-modal
        icon="warning"
        :title="__('lessons.restore_version') . '?'"
        :message="__('lessons.restore_confirm')"
        :confirmText="__('lessons.restore_version')"
        :cancelText="__('lessons.cancel')"
        :confirmRoute="route('lessons.history.restore', [$lesson, $history])"
        showVariable="showRestoreModal"
    />
</div>
```

---

## Styling

Components automatically apply correct colors based on `icon` type:

- **danger/delete**: Red gradient
- **warning/regenerate**: Yellow/orange gradient  
- **success/restore**: Green gradient
- **info**: Blue gradient

All modals use:
- Backdrop with blur
- Smooth appear/disappear animations
- Responsive design
- Beautiful icons with gradients

---

## Advantages:

✅ **DRY** - code not duplicated  
✅ **Consistency** - all modals look the same  
✅ **Easy to change** - changes in one place  
✅ **Flexibility** - can pass different parameters  
✅ **Type safety** - clear parameter structure
