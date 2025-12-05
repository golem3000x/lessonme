<?php

declare(strict_types=1);

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
        Schema::create(TableNameEnum::TEACHER_REVIEWS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained(TableNameEnum::USERS->value)->cascadeOnDelete();
            $table->foreignId('student_id')->constrained(TableNameEnum::USERS->value)->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'student_id']); // One review per teacher-student pair
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TEACHER_REVIEWS->value);
    }
};
