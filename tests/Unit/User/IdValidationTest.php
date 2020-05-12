<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Id;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class IdValidationTest extends TestCase
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

    public function testValidateId_1()
    {
        $this->assertTrue(self::$controller->validateId('1'), true);
    }

    public function testValidateId_2()
    {
        $this->expectException(Id::class);
        self::$controller->validateId('0');
    }

    public function testValidateId_3()
    {
        $this->expectException(Id::class);
        self::$controller->validateId(0);
    }

    public function testValidateId_4()
    {
        $this->expectException(Id::class);
        self::$controller->validateId(null);
    }

}
