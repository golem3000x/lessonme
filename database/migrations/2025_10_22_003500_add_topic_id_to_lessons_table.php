<?php

declare(strict_types=1);

use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(TableNameEnum::LESSONS->value, function (Blueprint $table): void {
            if (! Schema::hasColumn(TableNameEnum::LESSONS->value, 'topic_id')) {
                $table->foreignId('topic_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained(TableNameEnum::TOPICS->value)
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table(TableNameEnum::LESSONS->value, function (Blueprint $table): void {
            if (Schema::hasColumn(TableNameEnum::LESSONS->value, 'topic_id')) {
                $table->dropConstrainedForeignId('topic_id');
            }
        });
    }
};
