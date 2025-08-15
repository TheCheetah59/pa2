<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_inactive_users(): void
    {
        $admin = User::factory()->create(['is_activated' => true]);
        Sanctum::actingAs($admin);

        $inactive1 = User::factory()->create(['is_activated' => false]);
        $inactive2 = User::factory()->create(['is_activated' => false]);
        $active = User::factory()->create(['is_activated' => true]);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    public function test_activate_user(): void
    {
        $admin = User::factory()->create(['is_activated' => true]);
        Sanctum::actingAs($admin);

        $user = User::factory()->create(['is_activated' => false]);

        $this->patchJson('/api/users/' . $user->id . '/activate')
            ->assertStatus(200)
            ->assertJson(['user' => ['id' => $user->id, 'is_activated' => true]]);

        $this->assertTrue($user->fresh()->is_activated);
    }

    public function test_suspend_user(): void
    {
        $admin = User::factory()->create(['is_activated' => true]);
        Sanctum::actingAs($admin);

        $user = User::factory()->create(['is_activated' => true]);

        $this->patchJson('/api/users/' . $user->id . '/suspend')
            ->assertStatus(200)
            ->assertJson(['user' => ['id' => $user->id, 'is_activated' => false]]);

        $this->assertFalse($user->fresh()->is_activated);
    }
}