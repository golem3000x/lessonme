<?php

declare(strict_types=1);

use App\Enums\AvailableLanguageEnum;
use App\Enums\TableNameEnum;
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
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->string('zalo_user_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->unsignedTinyInteger('native_lang_id')->default(AvailableLanguageEnum::Vietnamese->value);
            $table->unsignedTinyInteger('learning_lang_id')->default(AvailableLanguageEnum::English->value);
        });
    }

    public function down(): void
    {
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->dropColumn(['zalo_user_id', 'avatar', 'native_lang_id', 'learning_lang_id']);
        });
    }
};
