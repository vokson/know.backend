<?php

namespace Tests\Feature\Tag;

use App\Article;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class AddTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $user = new Article();
        $user->id = 1;
        $user->version = 1;
        $user->subject = 'SUBJECT_1';
        $user->body = 'BODY_1';
        $user->user_id = 1;
        $user->is_attachment_exist = false;
        $user->save();

        $this->assertDatabaseHas('articles', [
            'id' => 1
        ]);

        $response = $this->json('POST', '/api/tag/create', [
            'name' => 'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $response = $this->json('POST', '/api/tag/create', [
            'name' => 'TAG_2'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_1',
            'article_id' => null
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_2',
            'article_id' => null
        ]);

        $response = $this->json('POST', '/api/tag/add', [
            'id' => 1,
            'name' => 'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);


        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_1',
            'id' => 1
        ]);

        $response = $this->json('POST', '/api/tag/add', [
            'id' => 2,
            'name' => 'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('6.3', $arr['error']);

        $response = $this->json('POST', '/api/tag/add', [
            'id' => 1,
            'name' => 'TAG_3'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('6.4', $arr['error']);

    }

}
