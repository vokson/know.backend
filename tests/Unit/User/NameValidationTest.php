<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Name;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class NameValidationTest extends TestCase
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

    public function testValidateName_1()
    {
        $this->assertTrue(self::$controller->validateName('John'), true);
    }

    public function testValidateName_2()
    {
        $this->expectException(Name::class);
        self::$controller->validateName(null);
    }

    public function testValidateName_3()
    {
        $this->expectException(Name::class);
        self::$controller->validateName(1);
    }

    public function testValidateName_4()
    {
        $this->expectException(Name::class);
        self::$controller->validateName('');
    }



}
