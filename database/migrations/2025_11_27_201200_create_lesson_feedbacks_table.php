<?php

declare(strict_types=1);

use App\Enums\FeedbackRatingEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableNameEnum::LESSON_FEEDBACKS->value, function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained(TableNameEnum::LESSONS->value)->onDelete('cascade');
            $table->foreignId('user_id')->constrained(TableNameEnum::USERS->value)->onDelete('cascade');
            $table->enum('rating', array_column(FeedbackRatingEnum::cases(), 'value'));
            $table->text('comment')->nullable();
            $table->timestamps();

            // One feedback per user per lesson
            $table->unique(['lesson_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_FEEDBACKS->value);
    }
};
