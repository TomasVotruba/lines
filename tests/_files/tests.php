<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
class ATest extends TestCase
{
    public function testFoo()
    {
    }

    #[DataProvider('barProvider')]
    #[Test]
    public function bar()
    {
    }

    protected function doSomething()
    {
    }

    public static function barProvider()
    {
    }
}
