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
        Schema::create(TableNameEnum::TOPIC_TRANSLATIONS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->string('language_code', 5)->index();
            $table->string('value');
            $table->timestamps();

            $table->unique(['topic_id', 'language_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TOPIC_TRANSLATIONS->value);
    }
};
