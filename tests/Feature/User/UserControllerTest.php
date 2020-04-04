<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testCreateUser()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals($arr['success'], 1);
    }
}
