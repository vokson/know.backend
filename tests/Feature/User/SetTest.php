<?php

namespace Tests\Feature\User;

use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class SetTest extends TestCase
{
    use RefreshDatabase;

    public function testSetUserWithMissedUser()
    {

        $response = $this->json('POST', '/api/user/set', [
            'id' => 1,
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'alex_pupkin@mail.ru',
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.8', $arr['error']);
    }

    public function testSetUser()
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
            'id' => 1,
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/'
        ]);

        $response = $this->json('POST', '/api/user/set', [
            'id' => 1,
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(1, $arr['success']);
    }

    public function testSetUserWithDuplicateEmail()
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
            'id' => 1,
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/'
        ]);

        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'alex_pupkin@mail.ru',
            'permission_expression' => '/().*/'
        ]);

        $response = $this->json('POST', '/api/user/set', [
            'id' => 2,
            'active' => 1,
            'name' => 'Alex',
            'surname' => 'Pupkin',
            'role' => 'engineer',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/().*/',
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.7', $arr['error']);
    }


}
