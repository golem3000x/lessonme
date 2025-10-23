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
        Schema::table(TableNameEnum::LESSONS->value, function (Blueprint $table): void {
            $table->string('lesson_name')->nullable()->after('topic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(TableNameEnum::LESSONS->value, function (Blueprint $table): void {
            $table->dropColumn('lesson_name');
        });
    }
};
