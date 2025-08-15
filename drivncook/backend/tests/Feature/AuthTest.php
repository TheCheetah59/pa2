<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ActivationLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);

        // Login should fail while inactive
        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(403);

        // Activate user
        $this->getJson('/api/activate/' . $user->activation_token)
            ->assertStatus(200);

        // Login should now succeed
        $login = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);
        $login->assertStatus(200)->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        // Me endpoint
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me')
            ->assertStatus(200)
            ->assertJson(['email' => 'jane@example.com']);
    }
}