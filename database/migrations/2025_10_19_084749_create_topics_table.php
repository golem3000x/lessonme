<?php

declare(strict_types=1);

use App\Enums\AvailableTopicEnum;
use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->unsignedTinyInteger('name');
            $table->timestamps();
        });

        DB::table(TableNameEnum::TOPICS->value)->insert([
            ['name' => AvailableTopicEnum::Programming, 'created_at' => now(), 'updated_at' => now()],
            ['name' => AvailableTopicEnum::Cooking, 'created_at' => now(), 'updated_at' => now()],
            ['name' => AvailableTopicEnum::Travel, 'created_at' => now(), 'updated_at' => now()],
            ['name' => AvailableTopicEnum::Art, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TOPICS->value);
    }
};
