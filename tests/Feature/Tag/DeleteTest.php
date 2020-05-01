<?php

namespace Tests\Feature\Tag;

use App\Tag;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class DeleteTest extends TestCase
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

        $response = $this->json('POST', '/api/tag/delete', [
            'name' =>'TAG_1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseMissing('tags', [
            'name' => 'TAG_1'
        ]);
    }

}
