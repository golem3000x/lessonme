<?php

declare(strict_types=1);

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
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_temp_guest')->default(false)->after('role');
            $table->timestamp('temp_guest_expires_at')->nullable()->after('is_temp_guest');

            // Add index for cleanup queries
            $table->index(['is_temp_guest', 'temp_guest_expires_at']);
        });
    }

    /**
     * Run the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['is_temp_guest', 'temp_guest_expires_at']);
            $table->dropColumn(['is_temp_guest', 'temp_guest_expires_at']);
        });
    }
};
