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
        Schema::create(TableNameEnum::LESSON_FEEDBACK_HISTORY->value, function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_history_id')->nullable()->constrained(TableNameEnum::LESSON_HISTORIES->value)->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained(TableNameEnum::LESSONS->value)->onDelete('cascade');
            $table->foreignId('user_id')->constrained(TableNameEnum::USERS->value)->onDelete('cascade');
            $table->enum('rating', array_column(FeedbackRatingEnum::cases(), 'value'));
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('original_feedback_id')->nullable()->comment('ID of original feedback before archiving');
            $table->timestamp('original_created_at')->nullable()->comment('Original creation timestamp');
            $table->timestamp('archived_at')->useCurrent()->comment('When feedback was moved to history');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::LESSON_FEEDBACK_HISTORY->value);
    }
};
