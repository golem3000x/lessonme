<?php
declare(strict_types=1);

use App\Enums\AvailableLanguageCodeEnum;
use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->string('zalo_user_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('native_lang')->default(AvailableLanguageCodeEnum::VI);
            $table->string('learning_lang')->default(AvailableLanguageCodeEnum::EN);
        });
    }

    public function down(): void
    {
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->dropColumn(['zalo_user_id', 'avatar', 'native_lang', 'learning_lang']);
        });
    }
};
