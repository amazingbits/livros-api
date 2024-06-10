<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookTest extends TestCase
{
    private array $headers = [
        "Content-Type" => "application/json",
        "Accept" => "application/json"
    ];
    private array $credentials = [
        'email' => 'user@user.com',
        'password' => 'user'
    ];

    private array $invalidData = [
        "titulo" => "exemplo",
        "indices" => [
            [
                "titulo" => "Índice 1",
            ],
            [
                "titulo" => "Índice 2",
                "pagina" => 10
            ]
        ],
    ];

    public function test_insert_book_without_credentials()
    {
        $url = $this->app->make('url')->to('/api/v1/livros');
        $response = $this->post($url, $this->invalidData, $this->headers);

        $response->assertStatus(401);
    }

    public function test_insert_book_with_invalid_data_structure()
    {
        // get Token
        $token = auth('api')->attempt($this->credentials);
        $this->headers['Authorization'] = "Bearer $token";

        $url = $this->app->make('url')->to('/api/v1/livros');
        $response = $this->post($url, $this->invalidData, $this->headers);
        $response->assertStatus(422);
    }
}
