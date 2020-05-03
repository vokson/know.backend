<?php

namespace Tests\Feature\User;

use App\Setting;
use App\User;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        $setting = new Setting();
        $setting->name = 'DEFAULT_PASSWORD';
        $setting->value = '1234';
        $setting->save();

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

        $response = $this->json('POST', '/api/user/login', [
        'email' => 'john_doe@mail.ru',
        'password' => 'WRONG_PASSWORD'
    ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.1', $arr['error']);

        $response = $this->json('POST', '/api/user/login', [
            'email' => 'john_doe@mail.ru',
            'password' => hash('sha256','1234')
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertEquals('John', $arr['name']);
        $this->assertEquals('Doe', $arr['surname']);
        $this->assertEquals('admin', $arr['role']);
        $this->assertEquals('john_doe@mail.ru', $arr['email']);
        $this->assertEquals('1', $arr['id']);
        $this->assertEquals('1', $arr['isDefaultPassword']);

        $user = User::find(1);
        $user->active = 0;
        $user->save();

        $response = $this->json('POST', '/api/user/login', [
            'email' => 'john_doe@mail.ru',
            'password' => hash('sha256','1234')
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.2', $arr['error']);

    }

    public function testLoginByToken()
    {
        $setting = new Setting();
        $setting->name = 'DEFAULT_PASSWORD';
        $setting->value = '1234';
        $setting->save();

        $setting = new Setting();
        $setting->name = 'TOKEN_LIFE_TIME';
        $setting->value = 10;
        $setting->save();

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

        $user = User::find(1);
        $correctToken = $user->access_token;

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' => ''
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.3', $arr['error']);

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' => 'WRONG_TOKEN'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.3', $arr['error']);

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' => $correctToken
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertEquals('John', $arr['name']);
        $this->assertEquals('Doe', $arr['surname']);
        $this->assertEquals('admin', $arr['role']);
        $this->assertEquals('john_doe@mail.ru', $arr['email']);
        $this->assertEquals('1', $arr['id']);
        $this->assertEquals('1', $arr['isDefaultPassword']);

        $user = User::find(1);
        $user->active = 0;
        $user->save();

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' => $correctToken
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.2', $arr['error']);

        $setting = Setting::where('name', 'TOKEN_LIFE_TIME')->first();
        $setting->value = 0;
        $setting->save();

        $user = User::find(1);
        $user->active = 1;
        $user->save();

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' => $correctToken
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.4', $arr['error']);

    }


}
