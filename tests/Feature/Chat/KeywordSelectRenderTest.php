<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\KeywordSelect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KeywordSelectRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_render_without_topic_shows_nothing(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(KeywordSelect::class, ['topic_id' => null])
            ->assertStatus(200);
    }
}
