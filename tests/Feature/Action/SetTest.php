<?php

namespace Tests\Feature\Action;

use App\Action;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class SetTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $this->assertDatabaseMissing('actions', [
            'name' => 'do',
            'role' => 'engineer'
        ]);

        $response = $this->json('POST', '/api/action/set', [
            'name' => 'do',
            'role' => 'engineer',
            'state' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('actions', [
            'name' => 'do',
            'role' => 'engineer'
        ]);

        $response = $this->json('POST', '/api/action/set', [
            'name' => 'do',
            'role' => 'engineer',
            'state' => 0
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseMissing('actions', [
            'name' => 'do',
            'role' => 'engineer'
        ]);

    }

    public function test_2()
    {
        $this->assertDatabaseMissing('actions', [
            'name' => 'do',
            'role' => 'engineer'
        ]);

        $response = $this->json('POST', '/api/action/set', [
            'name' => 'do',
            'role' => 'engineer',
            'state' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('actions', [
            'name' => 'do',
            'role' => 'engineer'
        ]);

        $response = $this->json('POST', '/api/action/set', [
            'name' => 'do',
            'role' => 'engineer',
            'state' => 1
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $this->assertDatabaseHas('actions', [
            'name' => 'do',
            'role' => 'engineer'
        ]);

       $this->assertEquals(1, Action::where('name', 'do')->where('role', 'engineer')->count());

    }

}
