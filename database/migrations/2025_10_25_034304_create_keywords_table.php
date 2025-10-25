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
        Schema::create(TableNameEnum::KEYWORDS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()
                ->constrained(TableNameEnum::TOPICS->value)
                ->nullOnDelete()
                ->after('user_id');
            $table->string('slug')->unique();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::KEYWORDS->value);
    }
};
