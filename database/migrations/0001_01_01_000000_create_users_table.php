<?php

declare(strict_types=1);

use App\Enums\System\TableNameEnum;
use App\Enums\System\UserRoleEnum;
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
        Schema::create(TableNameEnum::USERS->value, function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default(UserRoleEnum::STUDENT->value);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create(TableNameEnum::PASSWORD_RESET_TOKENS->value, function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create(TableNameEnum::SESSIONS->value, function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::USERS->value);
        Schema::dropIfExists(TableNameEnum::PASSWORD_RESET_TOKENS->value);
        Schema::dropIfExists(TableNameEnum::SESSIONS->value);
    }
};
