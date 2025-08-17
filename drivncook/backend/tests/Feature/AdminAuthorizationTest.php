<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'is_activated' => true,
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/users')->assertStatus(403);
    }

    public function test_non_admin_cannot_activate_user(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'is_activated' => true,
        ]);
        $target = User::factory()->create(['is_activated' => false]);
        Sanctum::actingAs($user);

        $this->patchJson('/api/users/' . $target->id . '/activate')->assertStatus(403);
    }

        public function test_non_admin_cannot_make_admin(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'is_activated' => true,
        ]);
        $target = User::factory()->create(['is_activated' => true]);
        Sanctum::actingAs($user);

        $this->patchJson('/api/users/' . $target->id . '/make-admin')->assertStatus(403);
    }



    public function test_suspended_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_activated' => false,
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/users')->assertStatus(403);
    }

    public function test_activated_admin_can_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_activated' => true,
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/users')->assertStatus(200);
    }
}