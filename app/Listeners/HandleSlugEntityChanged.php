<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\System\QueueNameEnum;
use App\Events\SlugEntityChanged;
use App\Jobs\Moderate\ModerateSlugEntityJob;
use App\Jobs\Translate\TranslateSlugEntityJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class HandleSlugEntityChanged
{
    public function handle(SlugEntityChanged $event): void
    {
        // Пропускаем seeders / миграции / artisan
        if (app()->runningInConsole()) {
            return;
        }

        Bus::chain([
            new ModerateSlugEntityJob($event->entity),
            new TranslateSlugEntityJob($event->entity),
        ])->catch(fn($e) => Log::error('SlugEntityChanged chain failed: ' . $e->getMessage()))
            ->dispatch()
            ->onQueue(QueueNameEnum::SLUG->value);
    }
}
