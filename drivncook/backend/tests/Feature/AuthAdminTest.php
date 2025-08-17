<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_route(): void
    {
        $this->getJson('/api/auth/admin')
            ->assertStatus(401);
    }

    public function test_inactive_admin_cannot_access_admin_route(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_activated' => false,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/auth/admin')
            ->assertStatus(403)
            ->assertJson(['message' => 'Account inactive.']);
    }

    public function test_non_admin_cannot_access_admin_route(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'is_activated' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/auth/admin')
            ->assertStatus(403);
    }

    public function test_active_admin_can_access_admin_route(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_activated' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/auth/admin')
            ->assertStatus(200)
            ->assertJson(['email' => $user->email]);
    }
}
