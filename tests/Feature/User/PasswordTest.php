<?php

namespace Tests\Feature\User;

use App\Setting;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test()
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
            'email' => 'john_doe@mail.ru'
        ]);

        $response = $this->json('POST', '/api/user/login', [
            'email' => 'john_doe@mail.ru',
            'password' => hash('sha256','1234')
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $token =  $arr['access_token'];

        $response = $this->json('POST', '/api/user/change/password', [
            'access_token' => $token,
            'password' => 'NEW_PASSWORD'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('users', [
            'email' => 'john_doe@mail.ru',
            'password' => 'NEW_PASSWORD'
        ]);

        $response = $this->json('POST', '/api/user/set/default/password', [
            'access_token' => $token,
            'id' => 1
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john_doe@mail.ru',
            'password' => hash('sha256','1234')
        ]);

    }

}
