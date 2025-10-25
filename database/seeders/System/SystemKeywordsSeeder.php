<?php

declare(strict_types=1);

namespace Database\Seeders\System;

use App\Enums\SystemKeywordEnum;
use App\Enums\TableNameEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemKeywordsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SystemKeywordEnum::cases() as $keyword_enum) {
            $topic = DB::table(TableNameEnum::TOPICS->value)
                ->where('slug', '=', Str::slug($keyword_enum->topic()->value))
                ->first();

            if (! $topic) {
                continue;
            }

            $slug = Str::slug($keyword_enum->value);

            $keyword = DB::table(TableNameEnum::KEYWORDS->value)
                ->where('slug', '=', $slug)
                ->where('topic_id', '=', $topic->id)
                ->first();

            $keyword_id = $keyword
                ? $keyword->id
                : DB::table(TableNameEnum::KEYWORDS->value)->insertGetId([
                    'topic_id' => $topic->id,
                    'is_system' => true,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $lang_files = [
                'en' => resource_path('lang/en/system-keywords.php'),
                'vi' => resource_path('lang/vi/system-keywords.php'),
            ];

            foreach ($lang_files as $locale => $file) {
                if (! file_exists($file)) {
                    continue;
                }

                $translations = include $file;
                $value = $translations[$keyword_enum->name] ?? $keyword_enum->value;

                $exists = DB::table(TableNameEnum::KEYWORD_TRANSLATIONS->value)
                    ->where('keyword_id', '=', $keyword_id)
                    ->where('language_code', '=', $locale)
                    ->exists();

                if (! $exists) {
                    DB::table(TableNameEnum::KEYWORD_TRANSLATIONS->value)->insert([
                        'keyword_id' => $keyword_id,
                        'language_code' => $locale,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
