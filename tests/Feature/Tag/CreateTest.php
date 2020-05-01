<?php

namespace Tests\Feature\Tag;

use App\Tag;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $response = $this->json('POST', '/api/tag/create', [
            'name' =>'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_1'
        ]);

        $response = $this->json('POST', '/api/tag/create', [
            'name' =>'TAG_2'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_2'
        ]);

        $response = $this->json('POST', '/api/tag/create', [
            'name' =>'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('tags', [
            'name' => 'TAG_1'
        ]);
    }

}
