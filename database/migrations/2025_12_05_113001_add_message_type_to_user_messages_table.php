<?php

declare(strict_types=1);

use App\Enums\System\MessageTypeEnum;
use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(TableNameEnum::USER_MESSAGES->value, function (Blueprint $table): void {
            $table->string('message_type', 20)
                ->default(MessageTypeEnum::TEXT->value)
                ->after('content');
        });
    }

    public function down(): void
    {
        Schema::table(TableNameEnum::USER_MESSAGES->value, function (Blueprint $table): void {
            $table->dropColumn('message_type');
        });
    }
};
