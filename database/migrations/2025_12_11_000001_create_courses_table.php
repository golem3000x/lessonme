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
        Schema::create(TableNameEnum::COURSES->value, function (Blueprint $table): void {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('language_level', 2)->nullable();
            $table->string('lesson_lang');
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('passing_score')->default(70);
            $table->timestamps();

            $table->index(['user_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::COURSES->value);
    }
};
