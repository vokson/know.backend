<?php

namespace Tests\Unit\Article;

use App\Exceptions\Article\Validation\Version;
use App\Http\Controllers\ArticleController;
use PHPUnit\Framework\TestCase;

class VersionValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new ArticleController();
        self::$functionName = 'validateVersion';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function test_1()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f(1), true);
    }

    public function test_2()
    {
        $this->expectException(Version::class);

        $f = self::$functionName;
        self::$controller->$f(0);
    }

    public function test_3()
    {
        $this->expectException(Version::class);
        $f = self::$functionName;
        self::$controller->$f('0');
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f(null), true);
    }

}
