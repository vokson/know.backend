<?php

namespace Tests\Feature\Article;

use App\User;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class SetTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $user = new User();
        $user->active = 1;
        $user->name = 'John';
        $user->surname = 'Doe';
        $user->role = 'admin';
        $user->email = 'john_doe@mail.ru';
        $user->permission_expression = '/.*/';
        $user->password = hash('sha256', '1234');
        $user->access_token = 'QWERTY';
        $user->save();

        $response = $this->json('POST', '/api/article/set', [
            'access_token' =>'QWERTY',
            'body' => 'BODY_1_1',
            'subject' => 'SUBJECT_1'
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'body' => 'BODY_1_1',
            'subject' => 'SUBJECT_1',
            'version' => 1,
            'user_id' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $response = $this->json('POST', '/api/article/set', [
            'access_token' =>'QWERTY',
            'body' => 'BODY_1_2',
            'subject' => 'SUBJECT_1'
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'body' => 'BODY_1_2',
            'subject' => 'SUBJECT_1',
            'version' => 2,
            'user_id' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
    }

}
