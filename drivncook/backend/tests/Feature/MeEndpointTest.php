<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_me_endpoint(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_activated' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/me')
            ->assertStatus(200)
            ->assertJson(['email' => $user->email]);
    }

    public function test_non_admin_can_access_me_endpoint(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'is_activated' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/me')
            ->assertStatus(200)
            ->assertJson(['email' => $user->email]);
    }
}