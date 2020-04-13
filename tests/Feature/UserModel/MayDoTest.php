<?php

namespace Tests\Feature\UserModel;

use App\Action;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MayDoTest extends TestCase
{

    use RefreshDatabase;

    public function test_1()
    {
        $user_1 = new User();
        $user_1->active = 1;
        $user_1->name = 'John';
        $user_1->surname = 'Doe';
        $user_1->role = 'admin';
        $user_1->email = 'john_doe@mail.ru';
        $user_1->permission_expression = '/.*/';
        $user_1->password = hash('sha256', '1234');
        $user_1->access_token = uniqid();
        $user_1->save();

        $user_2 = new User();
        $user_2->active = 1;
        $user_2->name = 'Alex';
        $user_2->surname = 'Pupkin';
        $user_2->role = 'engineer';
        $user_2->email = 'alex_pupkin@mail.ru';
        $user_2->permission_expression = '/().*/';
        $user_2->password = hash('sha256', '1234');
        $user_2->access_token = uniqid();
        $user_2->save();

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

        $this->assertEquals(false, $user_1->mayDo('ACTION_1'));
        $this->assertEquals(true, $user_1->mayDo('ACTION_2'));
        $this->assertEquals(false, $user_1->mayDo('ACTION_3'));
        $this->assertEquals(true, $user_2->mayDo('ACTION_1'));
        $this->assertEquals(true, $user_2->mayDo('ACTION_2'));
        $this->assertEquals(false, $user_2->mayDo('ACTION_3'));
    }

}
