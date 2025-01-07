<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAddTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can be added successfully
     *
     * @return void
     */
    public function test_user_can_be_added()
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('store.user'), [
            'fullname' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'role' => 0,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'fullname' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
        ]);
    }

    /**
     * Test that validation works for required fields
     *
     * @return void
     */
    public function test_validation_works_for_required_fields()
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('store.user'), [
            'fullname' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'role' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'fullname',
            'username',
            'email',
            'password',
            'role',
        ]);
    }

    /**
     * Test that email must be a valid email address
     *
     * @return void
     */
    public function test_email_must_be_valid()
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('store.user'), [
            'fullname' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'invalidemail',
            'password' => 'password',
            'role' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that password must be at least 8 characters long
     *
     * @return void
     */
    public function test_password_must_be_at_least_8_characters_long()
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('store.user'), [
            'fullname' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
            'password' => 'short',
            'role' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
}
