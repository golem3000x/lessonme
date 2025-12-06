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
        Schema::create(TableNameEnum::USER_LEARNING_LANGUAGES->value, function (Blueprint $table): void {
            $table->foreignId('user_id')->constrained(TableNameEnum::USERS->value)->cascadeOnDelete();
            $table->string('language');
            $table->primary(['user_id', 'language']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::USER_LEARNING_LANGUAGES->value);
    }
};
