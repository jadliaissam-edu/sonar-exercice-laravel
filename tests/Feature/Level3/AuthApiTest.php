<?php

namespace Tests\Feature\Level3;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_then_login_returns_the_same_token(): void
    {
        $register = $this->postJson('/api/register', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'secret-pass',
        ]);

        $register->assertCreated();

        $login = $this->postJson('/api/login', ['email' => 'alice@example.com', 'password' => 'secret-pass']);

        $login->assertOk();
        $this->assertSame($register->json('token'), $login->json('token'));
    }

    public function test_login_with_wrong_password_is_rejected(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'right-pass',
        ]);

        $this->postJson('/api/login', ['email' => 'bob@example.com', 'password' => 'wrong'])
            ->assertStatus(401);
    }
}
