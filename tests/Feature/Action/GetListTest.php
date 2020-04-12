<?php

namespace Tests\Feature\Action;

use App\Action;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class GetListTest extends TestCase
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
        $a->name = 'ACTION_2';
        $a->role = 'engineer';
        $a->save();

        $a = new Action();
        $a->name = 'ACTION_1';
        $a->role = null;
        $a->save();

        $a = new Action();
        $a->name = 'ACTION_2';
        $a->role = null;
        $a->save();

        $a = new Action();
        $a->name = 'ACTION_3';
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
            'name' => 'ACTION_2',
            'role' => 'engineer'
        ]);

        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_1',
            'role' => null
        ]);

        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_2',
            'role' => null
        ]);

        $this->assertDatabaseHas('actions', [
            'name' => 'ACTION_3',
            'role' => null
        ]);

        $response = $this->json('POST', '/api/list/roles', []);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $this->assertEquals(['admin', 'engineer'], $arr['items']);

        $response = $this->json('POST', '/api/list/actions', []);
        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
        $this->assertEquals(['ACTION_1', 'ACTION_2', 'ACTION_3'], $arr['items']);

    }
}
