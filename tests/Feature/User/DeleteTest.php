<?php

namespace Tests\Feature\User;

use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteUser()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john_doe@mail.ru',
        ]);

        $response = $this->json('POST', '/api/user/delete', [
            'id' => 1
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe@mail.ru',
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(1, $arr['success']);
    }

    public function testDeleteMissedUser()
    {
        $response = $this->json('POST', '/api/user/delete', [
            'id' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.8', $arr['error']);
    }

}
