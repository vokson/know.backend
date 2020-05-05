<?php

namespace Tests\Feature\User;

use App\Action;
use App\User;
Use App\Setting;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test()
    {
        $setting = new Setting();
        $setting->name = 'TOKEN_LIFE_TIME';
        $setting->value = 100;
        $setting->save();

        $user = new User();
        $user->active = 1;
        $user->name = 'John';
        $user->surname = 'Doe';
        $user->role = 'admin';
        $user->email = 'john_doe@mail.ru';
        $user->permission_expression = '/.*/';
        $user->password = hash('sha256', '1234');
        $user->access_token = 'TOKEN_1';
        $user->save();

        $user = new User();
        $user->active = 1;
        $user->name = 'Alex';
        $user->surname = 'Pupkin';
        $user->role = 'guest';
        $user->email = 'alex_pupkin@mail.ru';
        $user->permission_expression = '/.*/';
        $user->password = hash('sha256', '1234');
        $user->access_token = 'TOKEN_2';
        $user->save();

        // USER 1
        $response = $this->json('POST', '/api/user/login', [
            'email' => 'john_doe@mail.ru',
            'password' => hash('sha256','1234')
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $token_1 =  $arr['access_token'];

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' => $token_1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $token_1 =  $arr['access_token'];

        // USER 2
        $response = $this->json('POST', '/api/user/login', [
            'email' => 'alex_pupkin@mail.ru',
            'password' => hash('sha256','1234')
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $token_2 =  $arr['access_token'];

        $response = $this->json('POST', '/api/user/login/token', [
            'access_token' =>$token_2
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $token_2 =  $arr['access_token'];


        $user = new Action();
        $user->name = 'action/set';
        $user->save();

        $user = new Action();
        $user->name = 'article/set';
        $user->save();

        $user = new Action();
        $user->name = 'action/set';
        $user->role = 'admin';
        $user->save();

        $response = $this->json('POST', '/api/article/set', [
            'access_token' => $token_2,
            'body' => 'BODY',
            'subject' => 'SUBJECT'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.5', $arr['error']);

        $response = $this->json('POST', '/api/action/set', [
            'access_token' => $token_2,
            'name' => 'article/set',
            'role' => 'guest',
            'state' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('1.5', $arr['error']);

        $response = $this->json('POST', '/api/action/set', [
            'access_token' => $token_1,
            'name' => 'action/set',
            'role' => 'guest',
            'state' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $response = $this->json('POST', '/api/action/set', [
            'access_token' => $token_2,
            'name' => 'article/set',
            'role' => 'guest',
            'state' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $response = $this->json('POST', '/api/article/set', [
            'access_token' => $token_2,
            'body' => 'BODY',
            'subject' => 'SUBJECT'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'body' => 'BODY',
            'subject' => 'SUBJECT',
            'version' => 1,
            'user_id' => 2
        ]);
    }

}
