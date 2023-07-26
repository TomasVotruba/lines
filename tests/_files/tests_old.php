<?php

use PHPUnit\Framework\Attributes\Test;
class ATest extends PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
    }

    /**
     * @dataProvider barProvider
     */
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
