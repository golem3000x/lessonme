<?php
declare(strict_types=1);

use App\Enums\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(TableNameEnum::LESSONS->value, function (Blueprint $table): void {
            if (!Schema::hasColumn(TableNameEnum::LESSONS->value, 'topic_id')) {
                $table->foreignId('topic_id')->nullable()->constrained(TableNameEnum::TOPICS->value)->nullOnDelete()->after('user_id');
            }
        });

        // Backfill topic_id from existing topic name if possible
        if (Schema::hasColumn(TableNameEnum::LESSONS->value, 'topic')) {
            $lessons = DB::table(TableNameEnum::LESSONS->value)->select('id', 'topic')->whereNotNull('topic')->get();
            if ($lessons->count() > 0) {
                $topics = DB::table(TableNameEnum::TOPICS->value)->select('id', 'name')->get()->pluck('id', 'name');
                foreach ($lessons as $lesson) {
                    $topicId = $topics[$lesson->topic] ?? null;
                    if ($topicId) {
                        DB::table(TableNameEnum::LESSONS->value)
                            ->where('id', $lesson->id)
                            ->update(['topic_id' => $topicId]);
                    }
                }
            }
        }
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
