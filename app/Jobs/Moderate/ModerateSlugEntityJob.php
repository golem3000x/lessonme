<?php

declare(strict_types=1);

namespace App\Jobs\Moderate;

use App\Enums\ModerationStatusEnum;
use App\Models\Abstracts\Slug\SlugEntity;
use App\Notifications\Moderation\SlugEntityModerationNotification;
use App\Services\Slug\SlugService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ModerateSlugEntityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected SlugEntity $entity) {}

    public function handle(SlugService $openAI): void
    {
        $status = $this->processEntity($openAI);

        $this->notifyUser($status);
    }

    private function processEntity(SlugService $openAI): ModerationStatusEnum
    {
        try {
            if (! $this->entity->is_approved && ! $openAI->moderateSlug($this->entity->slug)) {
                return ModerationStatusEnum::REJECTED;
            }
            $this->entity->update(['is_approved' => true]);

            return ModerationStatusEnum::APPROVED;
        } catch (Exception $exception) {
            Log::error('Moderation error', [
                'entity_id' => $this->entity->id,
                'message' => $exception->getMessage(),
            ]);

            return ModerationStatusEnum::INVALID;
        }
    }

    private function notifyUser(ModerationStatusEnum $status): void
    {
        $user = $this->entity->user;

        if ($user) {
            $user->notify(new SlugEntityModerationNotification($this->entity, $status));
        }
    }
}
