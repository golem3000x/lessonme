<?php

declare(strict_types=1);

use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableNameEnum::COURSE_ASSIGNMENTS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained(TableNameEnum::COURSES->value)->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('due_date')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['course_id', 'student_id']);
            $table->index(['teacher_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::COURSE_ASSIGNMENTS->value);
    }
};
