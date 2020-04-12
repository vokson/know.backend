<?php

use App\Action;
use Illuminate\Database\Seeder;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
    }
}
