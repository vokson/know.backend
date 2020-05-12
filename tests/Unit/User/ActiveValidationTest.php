<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Active;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class ActiveValidationTest extends TestCase
{

    protected static $controller;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new UserController();
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
    }

    public function testValidateActive_1()
    {
        $this->assertTrue(self::$controller->validateActive('0'), true);
    }

    public function testValidateActive_2()
    {
        $this->assertTrue(self::$controller->validateActive('1'), true);
    }

    public function testValidateActive_3()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive('-1');
    }

    public function testValidateActive_4()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive('2');
    }

    public function testValidateActive_5()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive(0);
    }

    public function testValidateActive_6()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive(null);
    }

}
