<?php

declare(strict_types=1);

namespace App\Models\Lesson;

use App\Enums\Lesson\LessonAudioStatusEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lesson_id
 * @property string $audio_type
 * @property string $audio_key
 * @property string $file_path
 * @property int|null $duration
 * @property int|null $file_size
 * @property LessonAudioStatusEnum $status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Lesson $lesson
 */
class LessonAudioFile extends Model
{
    protected $table = TableNameEnum::LESSON_AUDIO_FILES->value;

    protected $fillable = [
        'lesson_id',
        'audio_type',
        'audio_key',
        'file_path',
        'duration',
        'file_size',
        'status',
        'error_message',
    ];

    protected $casts = [
        'duration' => 'integer',
        'file_size' => 'integer',
        'status' => LessonAudioStatusEnum::class,
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function isReady(): bool
    {
        return $this->status === LessonAudioStatusEnum::READY;
    }

    public function isPending(): bool
    {
        return $this->status === LessonAudioStatusEnum::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === LessonAudioStatusEnum::FAILED;
    }

    public function getPublicUrl(): ?string
    {
        if (! $this->isReady()) {
            return null;
        }

        return asset('storage/'.$this->file_path);
    }
}
