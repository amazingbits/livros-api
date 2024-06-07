<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    public function test_post_token_without_passing_data()
    {
        $response = $this->post('/api/v1/auth/token');

        $response->assertStatus(422);
    }

    public function test_post_token_with_incomplete_data()
    {
        $data = [
            "email" => "email@email.com"
        ];

        $response = $this->post('/api/v1/auth/token', $data);

        $response->assertStatus(422);
    }

    public function test_post_token_with_invalid_credentials()
    {
        $credentials = [
            "email" => "email",
            "password" => "123"
        ];

        $response = $this->post('/api/v1/auth/token', $credentials);

        $response->assertStatus(422);
        $this->assertNotEmpty(!empty($response->original));
        $this->assertNotEmpty(!empty($response->original["email"]));
        $this->assertEquals("The email must be a valid email address.", $response->original["email"][0]);
    }

    public function test_post_token_with_wrong_credentials()
    {
        $credentials = [
            "email" => "email@email.com",
            "password" => "123"
        ];

        $response = $this->post('/api/v1/auth/token', $credentials);

        $response->assertStatus(401);
        $this->assertNotEmpty(!empty($response->original));
        $this->assertNotEmpty(!empty($response->original["error"]));
        $this->assertEquals("Unauthorized", $response->original["error"]);
    }

    public function test_post_token_with_correct_credentials()
    {
        $credentials = [
            "email" => "user@user.com",
            "password" => "user"
        ];

        $response = $this->post('/api/v1/auth/token', $credentials);
        $this->assertNotEmpty(!empty($response->original));
        $this->assertNotEmpty(!empty($response->original["access_token"]));
        $response->assertStatus(200);
    }
}
