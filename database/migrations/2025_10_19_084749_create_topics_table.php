<?php
declare(strict_types=1);

use App\Enums\AvailableLanguageCodeEnum;
use App\Enums\TableNameEnum;
use App\Enums\TopicEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TableNameEnum::TOPICS->value, function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('language');
            $table->timestamps();
        });

        DB::table(TableNameEnum::TOPICS->value)->insert([
            ['name' => TopicEnum::PROGRAMMING, 'created_at' => now(), 'updated_at' => now(), 'language' => AvailableLanguageCodeEnum::EN],
            ['name' => TopicEnum::COOKING, 'created_at' => now(), 'updated_at' => now(), 'language' => AvailableLanguageCodeEnum::EN],
            ['name' => TopicEnum::TRAVEL, 'created_at' => now(), 'updated_at' => now(), 'language' => AvailableLanguageCodeEnum::EN],
            ['name' => TopicEnum::ART, 'created_at' => now(), 'updated_at' => now(), 'language' => AvailableLanguageCodeEnum::EN],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TOPICS->value);
    }
};
