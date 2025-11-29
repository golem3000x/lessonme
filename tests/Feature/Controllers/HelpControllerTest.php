<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_help_center(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('help.index'));

        $response->assertStatus(200);
        $response->assertViewIs('help.index');
    }

    public function test_getting_started_displays_getting_started_page(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('help.getting-started'));

        $response->assertStatus(200);
        $response->assertViewIs('help.getting-started');
    }

    public function test_for_students_displays_for_students_page(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('help.for-students'));

        $response->assertStatus(200);
        $response->assertViewIs('help.for-students');
    }

    public function test_for_teachers_displays_for_teachers_page(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('help.for-teachers'));

        $response->assertStatus(200);
        $response->assertViewIs('help.for-teachers');
    }

    public function test_exercise_types_displays_exercise_types_page(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('help.exercise-types'));

        $response->assertStatus(200);
        $response->assertViewIs('help.exercise-types');
    }

    public function test_faq_displays_faq_page(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('help.faq'));

        $response->assertStatus(200);
        $response->assertViewIs('help.faq');
    }

    public function test_all_help_pages_accessible_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $urls = [
            '/help',
            '/help/getting-started',
            '/help/for-students',
            '/help/for-teachers',
            '/help/exercise-types',
            '/help/faq',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertSuccessful();
        }
    }
}
