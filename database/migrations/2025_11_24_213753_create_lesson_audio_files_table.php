<?php

declare(strict_types=1);

use App\Enums\Lesson\LessonAudioStatusEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TableNameEnum::LESSON_AUDIO_FILES->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')->constrained(TableNameEnum::LESSONS->value)->cascadeOnDelete();
            $table->string('audio_type');
            $table->string('audio_key');
            $table->string('file_path');
            $table->integer('duration')->nullable();
            $table->integer('file_size')->nullable();
            $table->enum('status', [
                LessonAudioStatusEnum::PENDING->value,
                LessonAudioStatusEnum::READY->value,
                LessonAudioStatusEnum::FAILED->value,
            ])->default(LessonAudioStatusEnum::PENDING->value);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['lesson_id', 'audio_type', 'audio_key']);
            $table->index(['lesson_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_AUDIO_FILES->value);
    }
};
