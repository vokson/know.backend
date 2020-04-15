<?php

namespace Tests\Unit\Article;

use App\Exceptions\Article\Validation\UserId;
use App\Http\Controllers\ArticleController;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIdValidationTest extends TestCase
{

    use RefreshDatabase;

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new ArticleController();
        self::$functionName = 'validateUserId';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

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

        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f(1), true);

        $this->expectException(UserId::class);
        self::$controller->$f(2);
    }

    public function test_2()
    {
        $this->expectException(UserId::class);

        $f = self::$functionName;
        self::$controller->$f(0);
    }

    public function test_3()
    {
        $this->expectException(UserId::class);
        $f = self::$functionName;
        self::$controller->$f('0');
    }

    public function test_4()
    {
        $this->expectException(UserId::class);
        $f = self::$functionName;
        self::$controller->$f(null);
    }

}
