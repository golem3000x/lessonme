<?php

use App\Enums\System\TableNameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Убедимся, что расширение pgvector включено
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');

        $lesson_table = TableNameEnum::LESSONS->value;
        // Создаём индекс для быстрого поиска похожих embedding
        DB::statement("
            CREATE INDEX IF NOT EXISTS lesson_embedding_idx
            ON $lesson_table USING ivfflat (embedding vector_cosine_ops)
            WITH (lists = 100);
        ");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS lesson_embedding_idx;');
    }
};
