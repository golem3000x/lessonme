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
        Schema::create(TableNameEnum::LESSON_ATTEMPTS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained(TableNameEnum::LESSONS->value)
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->foreignId('assignment_id')
                ->nullable()
                ->constrained(TableNameEnum::LESSON_ASSIGNMENTS->value)
                ->nullOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->json('exercises_snapshot')->nullable()->comment('Exercises at attempt time');
            $table->json('user_answers')->nullable()->comment('User answers for all exercises');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'lesson_id']);
            $table->index('assignment_id');
            $table->index('is_completed');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_ATTEMPTS->value);
    }
};
