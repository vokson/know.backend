<?php

namespace Tests\Unit\Setting;

use App\Exceptions\Setting\Validation\Value;
use App\Http\Controllers\SettingController;
use PHPUnit\Framework\TestCase;

class ValueValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new SettingController();
        self::$functionName = 'validateValue';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function test_1()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f('John'), true);
    }

    public function test_2()
    {
        $f = self::$functionName;
        $this->expectException(Value::class);
        self::$controller->$f(null);
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(Value::class);
        self::$controller->$f(1);
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->expectException(Value::class);
        self::$controller->$f('');
    }


}
