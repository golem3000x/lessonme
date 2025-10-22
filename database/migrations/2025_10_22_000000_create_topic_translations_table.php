<?php
declare(strict_types=1);

use App\Enums\AvailableLanguageCodeEnum;
use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //отрефакторить
//        Schema::create(TableNameEnum::TOPIC_TRANSLATIONS->value, function (Blueprint $table): void {
//            $table->id();
//            $table->foreignId('topic_id')->constrained(TableNameEnum::TOPICS->value)->cascadeOnDelete();
//            $table->string('language', 5);
//            $table->string('name');
//            $table->timestamps();
//            $table->unique(['topic_id', 'language']);
//        });
//
//        // Backfill existing topics as EN translations if topics table exists
//        if (Schema::hasTable(TableNameEnum::TOPICS->value)) {
//            $topics = DB::table(TableNameEnum::TOPICS->value)->select('id', 'name', 'language')->get();
//            foreach ($topics as $topic) {
//                $lang = $topic->language ?: AvailableLanguageCodeEnum::EN->value;
//                // Insert existing translation (treat existing records as EN if language missing)
//                DB::table(TableNameEnum::TOPIC_TRANSLATIONS->value)->updateOrInsert(
//                    ['topic_id' => $topic->id, 'language' => $lang],
//                    ['name' => $topic->name, 'created_at' => now(), 'updated_at' => now()]
//                );
//            }
//
//            // Seed Vietnamese translations for known topics
//            $viMap = [
//                'Programming' => 'Lập trình',
//                'Cooking' => 'Nấu ăn',
//                'Travel' => 'Du lịch',
//                'Art' => 'Nghệ thuật',
//            ];
//
//            foreach ($viMap as $en => $vi) {
//                $row = DB::table(TableNameEnum::TOPICS->value)->where('name', $en)->first();
//                if ($row) {
//                    DB::table(TableNameEnum::TOPIC_TRANSLATIONS->value)->updateOrInsert(
//                        ['topic_id' => $row->id, 'language' => AvailableLanguageCodeEnum::VI->value],
//                        ['name' => $vi, 'created_at' => now(), 'updated_at' => now()]
//                    );
//                }
//            }
//        }
    }

    public function down(): void
    {
        Schema::dropIfExists(TableNameEnum::TOPIC_TRANSLATIONS->value);
    }
};
