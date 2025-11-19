<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Dto\LessonDto;
use App\Exceptions\LessonExistsException;
use App\Models\Lesson\Lesson;
use App\Models\User;
use Database\Seeders\GuestUserSeeder;

readonly class DemoLessonService
{
    public function __construct(
        private LessonService $lesson_service,
    ) {}

    /**
     * Create a demo lesson using the guest user
     *
     * @throws LessonExistsException
     */
    public function createDemoLesson(int $topic_id, LessonDto $lesson_dto): Lesson
    {
        $guest_user = User::where('email', GuestUserSeeder::GUEST_EMAIL)->firstOrFail();

        return $this->lesson_service->createLesson(
            $guest_user->id,
            $topic_id,
            $lesson_dto
        );
    }

    /**
     * Get the guest user
     */
    public function getGuestUser(): User
    {
        return User::where('email', GuestUserSeeder::GUEST_EMAIL)->firstOrFail();
    }
}
