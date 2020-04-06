<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Role;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class RoleValidationTest extends TestCase
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

    public function testValidateRole_1()
    {
        $this->assertTrue(self::$controller->validateRole('engineer'), true);
    }

    public function testValidateRole_2()
    {
        $this->expectException(Role::class);
        self::$controller->validateRole(null);
    }

    public function testValidateRole_3()
    {
        $this->expectException(Role::class);
        self::$controller->validateRole(1);
    }
    public function testValidateRole_4()
    {
        $this->expectException(Role::class);
        self::$controller->validateRole('');
    }

}
