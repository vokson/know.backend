<?php

namespace Tests\Unit\Article;

use App\Exceptions\Article\Validation\Query;
use App\Http\Controllers\ArticleController;
use PHPUnit\Framework\TestCase;

class OwnerValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new ArticleController();
        self::$functionName = 'validateOwner';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function test_1()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f('John'), true);
    }

    public function test_2()
    {
        $f = self::$functionName;
        $this->expectException(Query::class);
        self::$controller->$f(null);
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(Query::class);
        self::$controller->$f(1);
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->expectException(Query::class);
        self::$controller->$f('');
    }


}
