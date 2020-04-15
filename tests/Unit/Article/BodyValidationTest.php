<?php

namespace Tests\Unit\Article;

use App\Exceptions\Article\Validation\Body;
use App\Http\Controllers\ArticleController;
use PHPUnit\Framework\TestCase;

class BodyValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new ArticleController();
        self::$functionName = 'validateBody';
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
        $this->expectException(Body::class);
        self::$controller->$f(null);
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(Body::class);
        self::$controller->$f(1);
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->expectException(Body::class);
        self::$controller->$f('');
    }


}
