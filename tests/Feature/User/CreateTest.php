<?php

namespace Tests\Feature\User;

use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

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

        $this->assertDatabaseHas('users', [
            'email' => 'john_doe@mail.ru',
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(1, $arr['success']);
    }

    public function testCreateUserWithDuplicateEmail()
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
            'name' => 'John_2',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'John_2',
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.7', $arr['error']);
    }

    public function testCreateUserWithWrongActive()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 2,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe@mail.ru'
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.1', $arr['error']);
    }

    public function testCreateUserWithWrongName()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => '',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe@mail.ru'
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.3', $arr['error']);
    }

    public function testCreateUserWithWrongSurname()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => '',
            'role' => 'admin',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe@mail.ru'
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.4', $arr['error']);
    }

    public function testCreateUserWithWrongEmail()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'admin',
            'email' => 'john_doe#mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe#mail.ru'
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.2', $arr['error']);
    }

    public function testCreateUserWithWrongRole()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => '',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '/.*/',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe@mail.ru'
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.5', $arr['error']);
    }

    public function testCreateUserWithWrongPermissionExpression()
    {
        $response = $this->json('POST', '/api/user/create', [
            'active' => 1,
            'name' => 'John',
            'surname' => 'Doe',
            'role' => 'engineer',
            'email' => 'john_doe@mail.ru',
            'permission_expression' => '',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'john_doe@mail.ru'
        ]);


        $this->assertEquals($response->status(), 200);
        $arr = $response->json();

        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('2.6', $arr['error']);
    }

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


}
