<?php

namespace Tests\Unit\Article;

use App\Exceptions\Article\Validation\IsAttachmentExist;
use App\Http\Controllers\ArticleController;
use PHPUnit\Framework\TestCase;

class IsAttachmentExistValidationTest extends TestCase
{

    protected static $controller;
    protected static $functionName;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new ArticleController();
        self::$functionName = 'validateIsAttachmentExist';
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
        self::$functionName = null;
    }

    public function test_1()
    {
        $f = self::$functionName;
        $this->assertTrue(self::$controller->$f(0), true);
        $this->assertTrue(self::$controller->$f(1), true);
    }

    public function test_2()
    {
        $f = self::$functionName;
        $this->expectException(IsAttachmentExist::class);
        self::$controller->$f(null);
    }

    public function test_3()
    {
        $f = self::$functionName;
        $this->expectException(IsAttachmentExist::class);
        self::$controller->$f(-1);
    }

    public function test_4()
    {
        $f = self::$functionName;
        $this->expectException(IsAttachmentExist::class);
        self::$controller->$f(2);
    }

    public function test_5()
    {
        $f = self::$functionName;
        $this->expectException(IsAttachmentExist::class);
        self::$controller->$f('');
    }

    public function test_6()
    {
        $f = self::$functionName;
        $this->expectException(IsAttachmentExist::class);
        self::$controller->$f('1');
    }


}
