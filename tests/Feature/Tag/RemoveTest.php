<?php

namespace Tests\Feature\Tag;

use Tests\TestCase;
use App\Tag;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class RemoveTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $user = new Tag();
        $user->name = 'TAG_1';
        $user->save();

        $user = new Tag();
        $user->article_id = 1;
        $user->name = 'TAG_1';
        $user->save();

        $user = new Tag();
        $user->name = 'TAG_2';
        $user->save();

        $response = $this->json('POST', '/api/tag/remove', [
            'name' => 'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('6.1', $arr['error']);

        $response = $this->json('POST', '/api/tag/remove', [
            'id' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('6.2', $arr['error']);

        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_1',
            'article_id' => 1
        ]);

        $response = $this->json('POST', '/api/tag/remove', [
            'id' => 1,
            'name' => 'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseMissing('tags', [
            'name' => 'TAG_1',
            'article_id' => 1
        ]);

    }

}
