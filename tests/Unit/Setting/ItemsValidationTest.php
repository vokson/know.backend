<?php

namespace Tests\Unit\Setting;

use App\Exceptions\Setting\Validation\Items;
use App\Http\Controllers\SettingController;
use PHPUnit\Framework\TestCase;

class ItemsValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new SettingController();
        self::$functionName = 'validateItems';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function test_1()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f(array()), true);
    }

    public function test_2()
    {
        $f = self::$functionName;
        $this->expectException(Items::class);
        self::$controller->$f(null);
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(Items::class);
        self::$controller->$f(1);
    }

}
