<?php

declare(strict_types=1);

use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableNameEnum::USER_TOPIC_VISIBILITY->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'topic_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::USER_TOPIC_VISIBILITY->value);
    }
};
