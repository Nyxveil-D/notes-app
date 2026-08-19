<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register'), ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'password' => 'password', 'password_confirmation' => 'password']);
        $user = User::firstOrFail();
        $response->assertRedirect(route('notes.index'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Ada Lovelace', $user->name);
        $this->assertSame('ada@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_user_can_log_in(): void
    {
        $user = User::factory()->create(['email' => 'ada@example.com', 'password' => 'password']);
        $response = $this->post(route('login'), ['email' => 'ada@example.com', 'password' => 'password']);
        $response->assertRedirect(route('notes.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
