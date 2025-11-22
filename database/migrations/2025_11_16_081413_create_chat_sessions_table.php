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
        Schema::create(TableNameEnum::CHAT_SESSIONS->value, function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(TableNameEnum::USERS->value)->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::CHAT_SESSIONS->value);
    }
};
