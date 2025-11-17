<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\User;

/**
 * @property User|null $entity
 */
abstract class AbstractAuthNotification extends AbstractEntityNotification
{
    public function __construct(?User $entity, protected string $message = '')
    {
        parent::__construct($entity);
    }

    protected function getDefaultEntityName(): string
    {
        return 'user';
    }

    protected function getEventType(): string
    {
        return 'auth';
    }

    protected function generateId(): string|int
    {
        return uniqid("auth_{$this->getStatusEnum()->value}_");
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTranslationParams(): array
    {
        /** @var User|null $entity */
        $entity = $this->entity;
        
        return [
            'name' => $entity?->name ?? '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getCustomPayloadData(): array
    {
        /** @var User|null $entity */
        $entity = $this->entity;
        
        $data = [];
        
        if ($entity) {
            $data['user_id'] = $entity->id;
            $data['user_name'] = $entity->name;
        }
        
        // Always override message with custom greeting-based message
        // Auth notifications always use time-based greetings from GreetingService
        if (!empty($this->message)) {
            $data['message'] = $this->message;
        }
        
        return $data;
    }
}
