<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\System\UserRoleEnum;
use App\Http\Middleware\EnsureLessonAccess;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureLessonAccessTest extends TestCase
{
    use RefreshDatabase;

    private EnsureLessonAccess $middleware;

    private User $owner;

    private User $student;

    private User $other_user;

    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->middleware = new EnsureLessonAccess;

        $this->owner = User::factory()->create();
        $this->owner->assignRole(UserRoleEnum::TEACHER);

        $this->student = User::factory()->create();
        $this->student->assignRole(UserRoleEnum::STUDENT);

        $this->other_user = User::factory()->create();
        $this->other_user->assignRole(UserRoleEnum::STUDENT);

        $this->lesson = Lesson::factory()->create(['user_id' => $this->owner->id]);
    }

    private function createRequest(User $user, Lesson $lesson): Request
    {
        $request = Request::create('/lessons/'.$lesson->uuid, 'GET');
        $request->setUserResolver(fn () => $user);
        $request->setRouteResolver(function () use ($lesson) {
            $route = new \Illuminate\Routing\Route('GET', '/lessons/{lesson}', []);
            $route->bind($this->app->make(Request::class));
            $route->setParameter('lesson', $lesson);

            return $route;
        });

        return $request;
    }

    public function test_owner_can_access_lesson(): void
    {
        $request = $this->createRequest($this->owner, $this->lesson);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_assigned_student_can_access_lesson(): void
    {
        LessonAssignment::create([
            'lesson_id' => $this->lesson->id,
            'teacher_id' => $this->owner->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $request = $this->createRequest($this->student, $this->lesson);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_non_assigned_user_cannot_access_lesson(): void
    {
        $request = $this->createRequest($this->other_user, $this->lesson);

        $this->expectException(HttpException::class);

        $this->middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_guest_cannot_access_lesson(): void
    {
        $request = Request::create('/lessons/'.$this->lesson->uuid, 'GET');
        $request->setUserResolver(fn () => null);
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route('GET', '/lessons/{lesson}', []);
            $route->bind($this->app->make(Request::class));
            $route->setParameter('lesson', $this->lesson);

            return $route;
        });

        $this->expectException(HttpException::class);

        $this->middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_temp_guest_lesson_is_accessible(): void
    {
        $temp_guest = User::factory()->create(['is_temp_guest' => true]);
        $temp_lesson = Lesson::factory()->create(['user_id' => $temp_guest->id]);

        $request = $this->createRequest($this->other_user, $temp_lesson);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_passes_through_when_no_lesson_in_route(): void
    {
        $request = Request::create('/some-route', 'GET');
        $request->setUserResolver(fn () => $this->owner);
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route('GET', '/some-route', []);
            $route->bind($this->app->make(Request::class));

            return $route;
        });

        $response = $this->middleware->handle($request, fn () => new Response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }
}
