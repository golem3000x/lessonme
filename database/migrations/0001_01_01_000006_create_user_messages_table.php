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
        Schema::create(TableNameEnum::USER_MESSAGES->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sender_id')->constrained(TableNameEnum::USERS->value)->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained(TableNameEnum::USERS->value)->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'recipient_id']);
            $table->index(['recipient_id', 'sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::USER_MESSAGES->value);
    }
};
