<?php

namespace Tests\Feature\Article;

use App\User;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class DeleteTest extends TestCase
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
            'access_token' => 'QWERTY',
            'body' => 'BODY_1_1',
            'subject' => 'SUBJECT_1'
        ]);

        $response = $this->json('POST', '/api/article/set', [
            'id' => 1,
            'access_token' => 'QWERTY',
            'body' => 'BODY_1_2',
            'subject' => 'SUBJECT_1'
        ]);

        $response = $this->json('POST', '/api/article/set', [
            'access_token' => 'QWERTY',
            'body' => 'BODY_2_1',
            'subject' => 'SUBJECT_2'
        ]);


        $response = $this->json('POST', '/api/article/delete', ['id' => 1]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('5.9', $arr['error']);

        $response = $this->json('POST', '/api/article/delete', ['version' => 1]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('5.3', $arr['error']);

        $response = $this->json('POST', '/api/article/delete', ['id' => 3, 'version' => 1]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('5.7', $arr['error']);

        $response = $this->json('POST', '/api/article/delete', ['id' => 1, 'version' => 1]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('5.8', $arr['error']);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 1,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 2,
            'version' => 1,
        ]);

    }

    public function test_2()
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
            'access_token' => 'QWERTY',
            'body' => 'BODY_1_1',
            'subject' => 'SUBJECT_1'
        ]);

        $response = $this->json('POST', '/api/article/set', [
            'id' => 1,
            'access_token' => 'QWERTY',
            'body' => 'BODY_1_2',
            'subject' => 'SUBJECT_1'
        ]);

        $response = $this->json('POST', '/api/article/set', [
            'id' => 1,
            'access_token' => 'QWERTY',
            'body' => 'BODY_1_3',
            'subject' => 'SUBJECT_1'
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 1,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 2,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 3,
        ]);

        $response = $this->json('POST', '/api/article/delete', ['id' => 1, 'version' => 3]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 1,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => 1,
            'version' => 2,
        ]);

        $this->assertDatabaseMissing('articles', [
            'id' => 1,
            'version' => 3,
        ]);

    }



}
