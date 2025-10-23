<?php

declare(strict_types=1);

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
        Schema::create(TableNameEnum::CACHE->value, function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create(TableNameEnum::CACHE_LOCK->value, function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::CACHE->value);
        Schema::dropIfExists(TableNameEnum::CACHE_LOCK->value);
    }
};
