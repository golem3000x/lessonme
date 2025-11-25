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
        Schema::create(TableNameEnum::LESSONS->value, function (Blueprint $table): void {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('topic_id')->nullable();
            $table->string('lesson_name');
            $table->text('lesson_text');
            $table->string('language_level', 2);
            $table->string('lesson_lang');
            $table->json('grammar_data');
            $table->json('vocabulary');
            $table->json('dialogue');
            $table->json('exercises');
            $table->text('homework');
            $table->json('common_mistakes');
            $table->text('culture_notes');
            $table->vector('embedding', 1536)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSONS->value);
    }
};
