<?php

declare(strict_types=1);

namespace Database\Seeders\System;

use App\Enums\SystemTopicEnum;
use App\Enums\TableNameEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SystemTopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SystemTopicEnum::cases() as $enum) {
            DB::table(TableNameEnum::TOPICS->value)->updateOrInsert(
                ['slug' => $enum->value],
                ['is_system' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $lang_dirs = File::directories(lang_path());

        foreach ($lang_dirs as $lang_dir) {
            $locale = basename($lang_dir);
            $file = $lang_dir.'/system-topics.php';

            if (! File::exists($file)) {
                continue;
            }

            $translations = include $file;

            foreach ($translations as $key => $value) {
                $slug = Str::slug($key);

                $topic = DB::table(TableNameEnum::TOPICS->value)
                    ->where('slug', '=', $slug)
                    ->first();

                if (! $topic) {
                    continue;
                }

                $exists = DB::table(TableNameEnum::TOPIC_TRANSLATIONS->value)
                    ->where('topic_id', '=', $topic->id)
                    ->where('language_code', '=', $locale)
                    ->exists();

                if (! $exists) {
                    DB::table(TableNameEnum::TOPIC_TRANSLATIONS->value)->insert([
                        'topic_id' => $topic->id,
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
