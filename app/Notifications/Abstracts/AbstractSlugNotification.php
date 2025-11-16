<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Abstracts\Slug\Slug;

/**
 * @property Slug $entity
 */
abstract class AbstractSlugNotification extends AbstractEntityNotification
{
    public function __construct(Slug $entity)
    {
        parent::__construct($entity);
    }

    protected function getDefaultEntityName(): string
    {
        // Slug entities always have entity instance
        return '';
    }

    protected function generateId(): string|int
    {
        /** @var Slug $entity */
        $entity = $this->entity;
        
        return $entity->id;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTranslationParams(): array
    {
        /** @var Slug $entity */
        $entity = $this->entity;
        
        return [
            'slug' => $entity->slug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getCustomPayloadData(): array
    {
        /** @var Slug $entity */
        $entity = $this->entity;
        
        return [
            'slug' => $entity->slug,
        ];
    }
}
