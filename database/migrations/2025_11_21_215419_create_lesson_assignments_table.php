<?php

declare(strict_types=1);

use App\Enums\AssignmentStatusEnum;
use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableNameEnum::LESSON_ASSIGNMENTS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained(TableNameEnum::LESSONS->value)
                ->cascadeOnDelete();
            $table->foreignId('teacher_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->json('assigned_version')->nullable()->comment('Snapshot of exercises at assignment time');
            $table->timestamp('assigned_at');
            $table->timestamp('due_date')->nullable();
            $table->string('status', 20)->default(AssignmentStatusEnum::PENDING->value);
            $table->timestamps();

            $table->unique(['lesson_id', 'student_id']);
            $table->index('teacher_id');
            $table->index('student_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_ASSIGNMENTS->value);
    }
};
