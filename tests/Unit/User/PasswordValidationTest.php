<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Password;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class PasswordValidationTest extends TestCase
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

    public function testValidatePassword_1()
    {
        $this->assertTrue(self::$controller->validatePassword('password'), true);
    }

    public function testValidatePassword_2()
    {
        $this->expectException(Password::class);
        self::$controller->validatePassword(null);
    }

    public function testValidatePassword_3()
    {
        $this->expectException(Password::class);
        self::$controller->validatePassword(1);
    }
    public function testValidatePassword_4()
    {
        $this->expectException(Password::class);
        self::$controller->validatePassword('12');
    }

}
