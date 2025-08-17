<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ActivationLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_sends_activation_link(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->activation_token);

        Notification::assertSentTo($user, ActivationLink::class);
    }

    public function test_activation_and_login_flow(): void
    {
        Notification::fake();

        $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);

        // Login should fail while inactive
        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(403)
                 ->assertJson(['message' => 'Compte non activé']);

        // Activate user
        $this->getJson('/api/activate/' . $user->activation_token)
            ->assertStatus(200);

        // Login should now succeed
        $login = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);
        $login->assertStatus(200)
              ->assertJsonStructure(['token', 'name', 'email', 'role'])
              ->assertJson([
                  'name' => 'Jane Doe',
                  'email' => 'jane@example.com',
                  'role' => 'client',
              ]);

        $token = $login->json('token');

        // Me endpoint
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me')
            ->assertStatus(200)
            ->assertJson(['email' => 'jane@example.com']);
    }

    public function test_activation_with_invalid_token_returns_404(): void
    {
        $this->getJson('/api/activate/invalid-token')
            ->assertStatus(404)
            ->assertJson(['message' => 'Token non valide ou expiré']);
    }

    
    public function test_activation_with_expired_token_returns_404(): void
    {
        $user = User::factory()->create([
            'activation_token' => 'expired-token',
            'activation_token_expires_at' => now()->subDay(),
            'is_activated' => false,
        ]);

        $this->getJson('/api/activate/' . $user->activation_token)
            ->assertStatus(404)
            ->assertJson(['message' => 'Token non valide ou expiré']);
    }

    public function test_register_rejects_unsupported_role(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Invalid Role',
            'email' => 'invalid@example.com',
            'password' => 'password123',
            'role' => 'unsupported',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['role']);
    }

        public function test_register_rejects_admin_role(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Admin Attempt',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['role']);
    }


    public function test_login_returns_403_when_user_not_activated(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_activated' => false,
            'role' => 'client',
        ]);

        $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ])->assertStatus(403)
          ->assertJson(['message' => 'Compte non activé']);
    }

    public function test_login_returns_401_for_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'is_activated' => true,
            'role' => 'client',
        ]);

        $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ])->assertStatus(401)
          ->assertJson(['message' => 'Identifiants invalides']);
    }


    
    public function test_register_with_specific_role(): void
    {
        Notification::fake();

        $this->postJson('/api/register', [
            'name' => 'Alice Role',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'role' => 'franchise',
        ])->assertStatus(201);

        $user = User::where('email', 'alice@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('franchise', $user->role);
    }
}