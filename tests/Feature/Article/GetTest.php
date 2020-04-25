<?php

namespace Tests\Feature\Article;

use App\User;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class GetTest extends TestCase
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


        $response = $this->json('POST', '/api/article/get', ['id' => 1, 'version' => 1]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $this->assertEquals(1, $arr['id']);
        $this->assertEquals(1, $arr['version']);
        $this->assertEquals('SUBJECT_1', $arr['subject']);
        $this->assertEquals('BODY_1_1', $arr['body']);
        $this->assertEquals('Doe John', $arr['owner']);
        $this->assertEquals(0, $arr['is_attachment_exist']);

        $response = $this->json('POST', '/api/article/get', ['id' => 1, 'version' => 2]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $this->assertEquals(1, $arr['id']);
        $this->assertEquals(2, $arr['version']);
        $this->assertEquals('SUBJECT_1', $arr['subject']);
        $this->assertEquals('BODY_1_2', $arr['body']);
        $this->assertEquals('Doe John', $arr['owner']);
        $this->assertEquals(0, $arr['is_attachment_exist']);

        $response = $this->json('POST', '/api/article/get', ['id' => 1]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $this->assertEquals(1, $arr['id']);
        $this->assertEquals(2, $arr['version']);
        $this->assertEquals('SUBJECT_1', $arr['subject']);
        $this->assertEquals('BODY_1_2', $arr['body']);
        $this->assertEquals('Doe John', $arr['owner']);
        $this->assertEquals(0, $arr['is_attachment_exist']);

        $response = $this->json('POST', '/api/article/get', ['id' => 2]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $this->assertEquals(2, $arr['id']);
        $this->assertEquals(1, $arr['version']);
        $this->assertEquals('SUBJECT_2', $arr['subject']);
        $this->assertEquals('BODY_2_1', $arr['body']);
        $this->assertEquals('Doe John', $arr['owner']);
        $this->assertEquals(0, $arr['is_attachment_exist']);
    }

    public function testWithWrongId()
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

        $response = $this->json('POST', '/api/article/get', ['id' => 2]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('5.6', $arr['error']);
    }

    public function testWithWrongVersion()
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

        $response = $this->json('POST', '/api/article/get', ['id' => 1, 'version' => 2]);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('5.6', $arr['error']);
    }


}
