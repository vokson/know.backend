<?php

namespace Tests\Unit\Tag;

use App\Exceptions\Tag\Validation\Id;
use App\Http\Controllers\TagController;
use PHPUnit\Framework\TestCase;

class IdValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new TagController();
        self::$functionName = 'validateId';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function testValidateId_1()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f(1), true);
    }

    public function testValidateId_2()
    {
        $f = self::$functionName;
        $this->expectException(Id::class);
        self::$controller->$f(0);
    }

    public function testValidateId_3()
    {
        $f = self::$functionName;
        $this->expectException(Id::class);
        self::$controller->$f('0');
    }

    public function testValidateId_4()
    {
        $f = self::$functionName;
        $this->expectException(Id::class);
        self::$controller->$f(null);
    }

}
