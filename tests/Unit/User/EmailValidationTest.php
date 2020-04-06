<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Email;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class EmailValidationTest extends TestCase
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

    public function testValidateEmail_1()
    {
        $this->assertTrue(self::$controller->validateEmail('sample@mail.ru'), true);
    }

    public function testValidateEmail_2()
    {
        $this->expectException(Email::class);
        self::$controller->validateEmail(null);
    }

    public function testValidateEmail_3()
    {
        $this->expectException(Email::class);
        self::$controller->validateEmail(1);
    }

    public function testValidateEmail_4()
    {
        $this->expectException(Email::class);
        self::$controller->validateEmail('hello@server');
    }

}
