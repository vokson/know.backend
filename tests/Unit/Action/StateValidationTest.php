<?php

namespace Tests\Unit\Action;

use App\Exceptions\Action\Validation\State;
use App\Http\Controllers\ActionController;
use PHPUnit\Framework\TestCase;

class StateValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new ActionController();
        self::$functionName = 'validateState';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function test_1()
    {
        $f = self::$functionName;
        $this->assertTrue(true, self::$controller->$f(0));
    }

    public function test_2()
    {
        $f = self::$functionName;
        $this->assertTrue(true, self::$controller->$f(1));
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(State::class);
        self::$controller->$f(null);
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->expectException(State::class);
        self::$controller->$f('john');
    }

    public function test_5()
    {
        $f = self::$functionName;
        $this->expectException(State::class);
        self::$controller->$f('');
    }


}
