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
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table) {
            $table->boolean('is_banned')->default(false)->after('settings');
            $table->timestamp('banned_at')->nullable()->after('is_banned');
            $table->foreignId('banned_by')->nullable()->constrained(TableNameEnum::USERS->value)->nullOnDelete()->after('banned_at');
            $table->string('ban_reason')->nullable()->after('banned_by');
            $table->timestamp('ban_expires_at')->nullable()->after('ban_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(TableNameEnum::USERS->value, function (Blueprint $table) {
            $table->dropForeign(['banned_by']);
            $table->dropColumn(['is_banned', 'banned_at', 'banned_by', 'ban_reason', 'ban_expires_at']);
        });
    }
};
