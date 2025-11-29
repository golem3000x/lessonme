# Automatic Audio Files Deletion

## Overview

When a lesson is deleted, all associated audio files are automatically removed from both the database and storage.

## Implementation

### Model Event Hook

The `Lesson` model uses the `deleting` event to automatically clean up audio files:

```php
// app/Models/Lesson/Lesson.php

protected static function boot(): void
{
    parent::boot();

    // Automatically delete audio files when lesson is deleted
    static::deleting(function (Lesson $lesson): void {
        $audio_service = app(LessonAudioService::class);
        $audio_service->deleteAudioFiles($lesson);
    });
}
```

### Audio Deletion Service

The `LessonAudioService::deleteAudioFiles()` method handles:

1. **Physical file deletion** - Removes all audio files from storage directory:
   - Directory: `storage/app/public/lessons/{lesson_uuid}/audio/`
   - Includes vocabulary audio files
   - Includes gap_fill exercise audio files

2. **Database cleanup** - Deletes all `LessonAudioFile` records associated with the lesson

## Usage

No manual intervention required. Audio files are automatically deleted when:

- Lesson is deleted via Livewire component (`LessonIndex::deleteLesson()`)
- Lesson is deleted via Eloquent (`$lesson->delete()`)
- Lesson is deleted via query builder with model events enabled

## Storage Structure

```
storage/app/public/lessons/
└── {lesson-uuid}/
    └── audio/
        ├── vocabulary/
        │   ├── {md5-hash}.mp3
        │   └── ...
        └── exercises/
            ├── gap_fill_0.mp3
            ├── gap_fill_1.mp3
            └── ...
```

All files and directories under `lessons/{lesson-uuid}/audio/` are removed.

## Testing

Test coverage includes:

- `test_delete_audio_files_removes_database_records()` - Verifies database cleanup
- `test_delete_audio_files_removes_storage_directory()` - Verifies storage cleanup
- `test_lesson_deletion_automatically_deletes_audio_files()` - Verifies automatic deletion via model event

Run tests:

```bash
docker compose exec laravel.test php artisan test --filter=LessonAudioServiceTest
```

## Error Handling

If audio file deletion fails, the error is logged but the lesson deletion continues. This ensures data consistency even if storage operations fail.

Errors are logged in `storage/logs/laravel.log` with context:
- `lesson_id`
- `error` message
