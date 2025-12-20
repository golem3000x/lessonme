<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Enums\System\UserRoleEnum;
use App\Http\Middleware\EnsureUserIsTeacher;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureUserIsTeacherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_teacher_can_access(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole(UserRoleEnum::TEACHER);

        $this->actingAs($teacher);

        $middleware = new EnsureUserIsTeacher;
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, fn () => response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_student_cannot_access(): void
    {
        $student = User::factory()->create();
        $student->assignRole(UserRoleEnum::STUDENT);

        $this->actingAs($student);

        $middleware = new EnsureUserIsTeacher;
        $request = Request::create('/test', 'GET');

        $this->expectException(HttpException::class);

        $middleware->handle($request, fn () => response('OK'));
    }

    public function test_guest_cannot_access(): void
    {
        $middleware = new EnsureUserIsTeacher;
        $request = Request::create('/test', 'GET');

        $this->expectException(HttpException::class);

        $middleware->handle($request, fn () => response('OK'));
    }

    public function test_user_with_both_roles_can_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRoleEnum::TEACHER);
        $user->assignRole(UserRoleEnum::STUDENT);

        $this->actingAs($user);

        $middleware = new EnsureUserIsTeacher;
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, fn () => response('OK'));

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_admin_without_teacher_role_cannot_access(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRoleEnum::ADMIN);

        $this->actingAs($admin);

        $middleware = new EnsureUserIsTeacher;
        $request = Request::create('/test', 'GET');

        $this->expectException(HttpException::class);

        $middleware->handle($request, fn () => response('OK'));
    }

    public function test_forbidden_response_code(): void
    {
        $student = User::factory()->create();
        $student->assignRole(UserRoleEnum::STUDENT);

        $this->actingAs($student);

        $middleware = new EnsureUserIsTeacher;
        $request = Request::create('/test', 'GET');

        try {
            $middleware->handle($request, fn () => response('OK'));
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }
    }
}
