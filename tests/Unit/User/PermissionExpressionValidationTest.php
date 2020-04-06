<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\PermissionExpression;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class PermissionExpressionValidationTest extends TestCase
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

    public function testValidatePermissionExpression_1()
    {
        $this->assertTrue(self::$controller->validatePermissionExpression('/.*/'), true);
    }

    public function testValidatePermissionExpression_2()
    {
        $this->expectException(PermissionExpression::class);
        self::$controller->validatePermissionExpression(null);
    }

    public function testValidatePermissionExpression_3()
    {
        $this->expectException(PermissionExpression::class);
        self::$controller->validatePermissionExpression(1);
    }



}
