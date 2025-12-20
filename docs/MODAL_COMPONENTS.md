# Modal Components - Документация

Два переиспользуемых компонента для модальных окон подтверждения.

---

## 1. `<x-confirm-modal>` - Для обычных форм

Используется для модалов с обычными HTML формами (POST, DELETE запросы).

### Параметры:

| Параметр | Тип | По умолчанию | Описание |
|----------|-----|--------------|----------|
| `icon` | string | `'warning'` | Тип иконки: `warning`, `danger`, `success`, `info` |
| `title` | string | *обязательный* | Заголовок модала |
| `message` | string | *обязательный* | Текст сообщения |
| `confirmText` | string | `'Confirm'` | Текст кнопки подтверждения |
| `cancelText` | string | `'Cancel'` | Текст кнопки отмены |
| `confirmRoute` | string | `null` | Route для отправки формы |
| `confirmMethod` | string | `'POST'` | HTTP метод (POST, DELETE, PUT и т.д.) |
| `showVariable` | string | `'showModal'` | Имя переменной Alpine.js для управления |

### Пример использования:

```blade
<div x-data="{ showDeleteModal: false }">
    {{-- Кнопка открытия модала --}}
    <button @click="showDeleteModal = true">
        Delete
    </button>

    {{-- Модальное окно --}}
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

### Типы иконок:

#### `icon="danger"` или `icon="delete"`
- Красная иконка удаления
- Используется для деструктивных действий

#### `icon="warning"` или `icon="regenerate"` (по умолчанию)
- Желтая/оранжевая иконка предупреждения
- Используется для действий с предупреждением

#### `icon="success"` или `icon="restore"`
- Зеленая иконка успеха
- Используется для восстановления или положительных действий

#### `icon="info"`
- Синяя информационная иконка
- Используется для информационных сообщений

---

## 2. `<x-confirm-modal-livewire>` - Для Livewire

Используется для модалов внутри Livewire компонентов.

### Параметры:

| Параметр | Тип | По умолчанию | Описание |
|----------|-----|--------------|----------|
| `icon` | string | `'warning'` | Тип иконки (те же, что и в обычном модале) |
| `title` | string | *обязательный* | Заголовок модала |
| `message` | string | *обязательный* | Текст сообщения |
| `confirmText` | string | `'Confirm'` | Текст кнопки подтверждения |
| `cancelText` | string | `'Cancel'` | Текст кнопки отмены |
| `confirmMethod` | string | `null` | Имя метода Livewire для подтверждения |
| `cancelMethod` | string | `null` | Имя метода Livewire для отмены |

### Пример использования в Livewire:

```blade
{{-- В Livewire компоненте --}}
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

### В PHP классе Livewire:

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
        // Логика удаления
        $this->show_delete_confirm = false;
    }
}
```

---

## Примеры реального использования:

### 1. Удаление урока (Livewire)
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

### 2. Регенерация урока (Livewire)
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

### 3. Восстановление версии (обычная форма)
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

## Стилизация

Компоненты автоматически применяют правильные цвета на основе типа `icon`:

- **danger/delete**: Красный градиент
- **warning/regenerate**: Желтый/оранжевый градиент  
- **success/restore**: Зеленый градиент
- **info**: Синий градиент

Все модалы используют:
- Backdrop с размытием
- Плавные анимации появления/исчезновения
- Адаптивный дизайн
- Красивые иконки с градиентами

---

## Преимущества:

✅ **DRY** - код не дублируется  
✅ **Консистентность** - все модалы выглядят одинаково  
✅ **Легко менять** - изменения в одном месте  
✅ **Гибкость** - можно передавать разные параметры  
✅ **Типобезопасность** - четкая структура параметров
