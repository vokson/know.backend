<?php

namespace Tests\Unit\User;

use App\Exceptions\User\Validation\Id;
use App\Exceptions\User\Validation\Active;
use App\Exceptions\User\Validation\Name;
use App\Exceptions\User\Validation\Surname;
use App\Exceptions\User\Validation\Role;
use App\Exceptions\User\Validation\Password;
use App\Exceptions\User\Validation\PermissionExpression;
use App\Exceptions\User\Validation\Email;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
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
        $this->assertTrue(self::$controller->validateId(1), true);
    }

    public function testValidateId_2()
    {
        $this->expectException(Id::class);
        self::$controller->validateId(0);
    }

    public function testValidateId_3()
    {
        $this->expectException(Id::class);
        self::$controller->validateId('0');
    }

    public function testValidateId_4()
    {
        $this->expectException(Id::class);
        self::$controller->validateId(null);
    }

    public function testValidateActive_1()
    {
        $this->assertTrue(self::$controller->validateActive(0), true);
    }

    public function testValidateActive_2()
    {
        $this->assertTrue(self::$controller->validateActive(1), true);
    }

    public function testValidateActive_3()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive(-1);
    }

    public function testValidateActive_4()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive(2);
    }

    public function testValidateActive_5()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive('0');
    }

    public function testValidateActive_6()
    {
        $this->expectException(Active::class);
        self::$controller->validateActive(null);
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
