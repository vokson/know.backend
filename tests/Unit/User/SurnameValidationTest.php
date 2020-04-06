<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Surname;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class SurnameValidationTest extends TestCase
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

    public function testValidateSurname_1()
    {
        $this->assertTrue(self::$controller->validateSurname('Doe'), true);
    }

    public function testValidateSurname_2()
    {
        $this->expectException(Surname::class);
        self::$controller->validateSurname(null);
    }

    public function testValidateSurname_3()
    {
        $this->expectException(Surname::class);
        self::$controller->validateSurname(1);
    }

    public function testValidateSurname_4()
    {
        $this->expectException(Surname::class);
        self::$controller->validateSurname('');
    }

}
