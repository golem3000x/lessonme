<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ModerationStatusEnum;
use App\Enums\SystemLanguageEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Services\OpenAi\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class ModerateSlugEntityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected SlugEntity $entity) {}

    public function handle(OpenAIService $openAI): void
    {
        $status = $this->processEntity($openAI);

        $this->notifyUser($status);
    }

    private function processEntity(OpenAIService $openAI): ModerationStatusEnum
    {
        $translation = $this->entity->translation();
        $targetLang = SystemLanguageEnum::tryFrom($this->entity->user?->learning_lang ?? '');

        if (! $translation || ! $targetLang) {
            return ModerationStatusEnum::INVALID;
        }

        $originalValue = $translation->value;

        if (! $this->entity->is_approved && ! $openAI->moderateText($originalValue)) {
            return ModerationStatusEnum::REJECTED;
        }

        $this->entity->update(['is_approved' => true]);

        $translated = $openAI->translateText($originalValue, $targetLang);

        if (! empty($translated)) {
            $translationModelClass = $this->getTranslationModelClass();
            $entityIdColumn = $this->getEntityIdColumn();

            $translationModelClass::updateOrCreate(
                [
                    $entityIdColumn => $this->entity->id,
                    'language_code' => $targetLang->value,
                ],
                [
                    'value' => $translated,
                ]
            );
        } else {
            return ModerationStatusEnum::INVALID;
        }

        return ModerationStatusEnum::APPROVED;
    }

    private function notifyUser(ModerationStatusEnum $status): void
    {
        $user = $this->entity->user;

        match ($status) {
            ModerationStatusEnum::APPROVED => $user->notify($this->getApprovedNotification()),

            ModerationStatusEnum::REJECTED => $user->notify($this->getRejectedNotification()),

            ModerationStatusEnum::INVALID => $user->notify($this->getErrorNotification()),
        };
    }

    /**
     * Get the translation model class name (e.g., TopicTranslation::class)
     */
    abstract protected function getTranslationModelClass(): string;

    /**
     * Get the entity ID column name (e.g., 'topic_id', 'keyword_id')
     */
    abstract protected function getEntityIdColumn(): string;

    /**
     * Get notification for approved status
     */
    abstract protected function getApprovedNotification(): Notification;

    /**
     * Get notification for rejected status
     */
    abstract protected function getRejectedNotification(): Notification;

    /**
     * Get notification for error/invalid status
     */
    abstract protected function getErrorNotification(): Notification;
}
