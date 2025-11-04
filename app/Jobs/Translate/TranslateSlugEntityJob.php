<?php

declare(strict_types=1);

namespace App\Jobs\Translate;

use App\Enums\TranslateStatusEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Notifications\Translate\SlugEntityTranslateNotification;
use App\Services\OpenAi\OpenAIService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateSlugEntityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected SlugEntity $entity) {}

    public function handle(OpenAIService $openAI): void
    {
        $status = $this->processEntity($openAI);

        $this->notifyUser($status);
    }

    private function processEntity(OpenAIService $openAI): TranslateStatusEnum
    {
        if ($this->entity->is_approved) {
            try {
                $translations = $openAI->translateSlugAllLanguages($this->entity->slug);

                if (empty($translations) || ! is_array($translations)) {
                    return TranslateStatusEnum::INVALID;
                }

                $translation_model_class = $this->entity->translations()->getRelated();
                $entity_id_column = $this->entity->translations()->getForeignKeyName();

                $data = collect($translations)
                    ->map(fn ($value, $language_code) => [
                        $entity_id_column => $this->entity->id,
                        'language_code' => $language_code,
                        'value' => $value,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ])
                    ->values()
                    ->toArray();

                $translation_model_class::upsert(
                    $data,
                    [$entity_id_column, 'language_code'],
                    ['value', 'updated_at']
                );

                return TranslateStatusEnum::SUCCESSFULLY;
            } catch (Exception $exception) {
                Log::error('Translation error', [
                    'entity_id' => $this->entity->id,
                    'message' => $exception->getMessage(),
                ]);

                return TranslateStatusEnum::INVALID;
            }
        }

        return TranslateStatusEnum::INVALID;
    }

    private function notifyUser(TranslateStatusEnum $status): void
    {
        $user = $this->entity->user;

        $user->notify(new SlugEntityTranslateNotification($this->entity, $status));
    }
}
