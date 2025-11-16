<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Lesson\Lesson;

/**
 * @property Lesson|null $entity
 */
abstract class AbstractLessonNotification extends AbstractEntityNotification
{
    public function __construct(?Lesson $entity)
    {
        parent::__construct($entity);
    }

    protected function getDefaultEntityName(): string
    {
        return 'lesson';
    }

    protected function generateId(): string|int
    {
        return uniqid("lesson_{$this->getEventType()}_");
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTranslationParams(): array
    {
        /** @var Lesson|null $entity */
        $entity = $this->entity;
        
        return [
            'name' => $entity !== null ? $entity->lesson_name : '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getCustomPayloadData(): array
    {
        /** @var Lesson|null $entity */
        $entity = $this->entity;
        
        if (!$entity) {
            return [];
        }

        return [
            'lesson_id' => $entity->id,
            'lesson_url' => route('lessons.show', $entity->id),
            'lesson_name' => $entity->lesson_name,
        ];
    }
}
