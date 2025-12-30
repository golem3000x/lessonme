<?php

declare(strict_types=1);

use App\Enums\System\SystemLanguageEnum;
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
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->string('zalo_user_id')->nullable()->unique();
            $table->string('facebook_id')->nullable()->unique();
            $table->text('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('bio')->nullable();
            $table->string('native_lang')->default(SystemLanguageEnum::VIETNAMESE->value);
            $table->boolean('is_looking_for_teacher')->default(false);
            $table->boolean('is_accepting_students')->default(false);
            $table->json('settings')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->dropColumn([
                'zalo_user_id',
                'facebook_id',
                'avatar',
                'date_of_birth',
                'bio',
                'native_lang',
                'is_looking_for_teacher',
                'is_accepting_students',
                'settings',
            ]);
        });
    }
};
