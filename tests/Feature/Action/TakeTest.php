<?php

namespace Tests\Feature\Action;

use App\Action;
use App\Http\Controllers\ActionController;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class TakeTest extends TestCase
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


        $this->assertEquals(true, ActionController::take('engineer', 'ACTION_1'));
        $this->assertEquals(false, ActionController::take('engineer', 'ACTION_2'));
        $this->assertEquals(false, ActionController::take('engineer', 'ACTION_3'));

        $this->assertEquals(false, ActionController::take('admin', 'ACTION_1'));
        $this->assertEquals(true, ActionController::take('admin', 'ACTION_2'));
        $this->assertEquals(false, ActionController::take('admin', 'ACTION_3'));

    }
}
