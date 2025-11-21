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
        Schema::create(TableNameEnum::TEACHER_STUDENT->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained(TableNameEnum::USERS->value)
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['teacher_id', 'student_id']);
            $table->index('teacher_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TEACHER_STUDENT->value);
    }
};
