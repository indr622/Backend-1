<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{

    protected $token;
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_login(): void
    {
        $response = $this->post('/api/login', [
            'email' => 'admin@admin.com',
            'password' => 'password'
        ]);

        $this->token = $response->json('token');

        $response->assertStatus(201);
    }

    public function test_get_all_posts(): void
    {
        $this->test_login();
        $response = $this->get('/api/post', [
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
    }

    public function test_create_post(): void
    {
        $this->test_login();
        $response = $this->post('/api/post', [
            'title' => 'test title',
            'body' => 'test description',
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(201);
    }

    public function test_update_post()
    {
        $this->test_login();
        $response = $this->put('/api/post/1', [
            'title' => 'test title',
            'body' => 'test description',
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
    }

    public function test_delete_post()
    {
        $this->test_login();
        $response = $this->delete('/api/post/1', [
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
    }
}
