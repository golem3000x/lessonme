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
        Schema::create(TableNameEnum::LESSON_HISTORIES->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('topic_id');
            $table->string('lesson_name')->nullable();
            $table->text('lesson_text')->nullable();
            $table->json('grammar_data')->nullable();
            $table->json('vocabulary')->nullable();
            $table->json('quiz')->nullable();
            $table->vector('embedding', 1536)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_HISTORIES->value);
    }
};
