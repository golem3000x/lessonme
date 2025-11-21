<?php

declare(strict_types=1);

use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableNameEnum::LESSON_SCORES->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attempt_id')
                ->constrained(TableNameEnum::LESSON_ATTEMPTS->value)
                ->cascadeOnDelete();
            $table->foreignId('lesson_id')
                ->constrained(TableNameEnum::LESSONS->value)
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->foreignId('teacher_id')
                ->nullable()
                ->constrained(TableNameEnum::USERS->value)
                ->nullOnDelete()
                ->comment('For manual grading by teacher');
            $table->unsignedInteger('total_questions');
            $table->unsignedInteger('correct_answers');
            $table->decimal('score_percentage', 5, 2)->comment('0.00 to 100.00');
            $table->boolean('auto_graded')->default(true);
            $table->text('teacher_comment')->nullable();
            $table->timestamp('graded_at');
            $table->timestamps();

            $table->index('attempt_id');
            $table->index(['user_id', 'lesson_id']);
            $table->index('teacher_id');
            $table->index('score_percentage');
            $table->index('graded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_SCORES->value);
    }
};
