<?php

namespace Tests\Feature\User;

use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTest extends TestCase
{
    use RefreshDatabase;

    public function testGetZeroUsers()
    {
        $response = $this->json('POST', '/api/user/get', ['role' => 'engineer']);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(1, $arr['success']);
        $this->assertEquals([], $arr['items']);
    }

    public function testGetUsersWithFilter()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/',
        ]);

        $response = $this->json('POST', '/api/user/get', ['role' => 'engineer']);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(1, $arr['success']);

        $this->assertEquals(
            [
                'id' => 2,
                'active' => 1,
                'name' => 'Alex',
                'surname' => 'Pupkin',
                'role' => 'engineer',
                'email' => 'alex_pupkin@mail.ru',
                'permission_expression' => '/().*/'
            ],
            $arr['items'][0]
        );
    }

    public function testGetUsers()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/',
        ]);

        $response = $this->json('POST', '/api/user/get', []);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(1, $arr['success']);

        $this->assertEquals(
            [
                'id' => 1,
                'active' => 1,
                'name' => 'John',
                'surname' => 'Doe',
                'role' => 'admin',
                'email' => 'john_doe@mail.ru',
                'permission_expression' => '/.*/'
            ],
            $arr['items'][0]
        );

        $this->assertEquals(
            [
                'id' => 2,
                'active' => 1,
                'name' => 'Alex',
                'surname' => 'Pupkin',
                'role' => 'engineer',
                'email' => 'alex_pupkin@mail.ru',
                'permission_expression' => '/().*/'
            ],
            $arr['items'][1]
        );
    }
}
