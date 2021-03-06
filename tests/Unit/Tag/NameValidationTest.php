<?php

namespace Tests\Unit\Tag;

use App\Exceptions\Tag\Validation\Name;
use App\Http\Controllers\TagController;
use PHPUnit\Framework\TestCase;

class NameValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new TagController();
        self::$functionName = 'validateName';
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
        $this->expectException(Name::class);
        self::$controller->$f(null);
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(Name::class);
        self::$controller->$f(1);
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->expectException(Name::class);
        self::$controller->$f('');
    }


}
