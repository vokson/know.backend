<?php

namespace Tests\Feature\Action;

use App\Action;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class GetTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $a = new Action();
        $a->name = 'ACTION_1';
        $a->role = 'engineer';
        $a->save();

        $a = new Action();
        $a->name = 'ACTION_2';
        $a->role = 'admin';
        $a->save();

        $a = new Action();
        $a->name = 'ACTION_1';
        $a->role = null;
        $a->save();

        $a = new Action();
        $a->name = 'ACTION_2';
        $a->role = null;
        $a->save();


        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_1',
            'role' => 'engineer'
        ]);

        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_2',
            'role' => 'admin'
        ]);

        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_1',
            'role' => null
        ]);

        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_2',
            'role' => null
        ]);

        $response = $this->json('POST', '/api/action/get', []);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $items = $arr['items'];

        $this->assertEquals(['name' => 'ACTION_1', 'role' => 'engineer'], $items[0]);
        $this->assertEquals(['name' => 'ACTION_2', 'role' => 'admin'], $items[1]);

    }
}
