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
        Schema::create(TableNameEnum::TOPICS->value, function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('slug');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            // Unique constraint: user can't have duplicate slugs
            $table->unique(['user_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TOPICS->value);
    }
};
