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
        Schema::create(TableNameEnum::MESSAGES->value, function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained(TableNameEnum::CHAT_SESSIONS->value)->onDelete('cascade');
            $table->foreignId('user_id')->constrained(TableNameEnum::USERS->value)->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_from_admin')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index('chat_session_id');
            $table->index('user_id');
            $table->index(['is_from_admin', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::MESSAGES->value);
    }
};
