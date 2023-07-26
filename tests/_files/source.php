<?php
namespace a\name\space;

/*
 * A comment.
 */

define('A_GLOBAL_CONSTANT', 'foo');


function &a_global_function()
{
    $a = AClass::CLASS;
}

interface AnInterface
{
}

abstract class AnAbstractClass
{
}

/**
 * A comment.
 */
class AClass extends AnAbstractClass implements AnInterface
{
    final public const A_CLASS_CONSTANT = 'bar';

    private static array $a = [];

    public static function aStaticMethod()
    {
        $o = null;
        $m = null;
        global $foo;

        $a = $_GET['a'];
        $GLOBALS['bar'] = A_GLOBAL_CONSTANT;
        // Another comment
        $o->m();
        $o->$m();
        $o->a;
        $o->$a;
    }

    public function aPublicMethod()
    {
        $m = null;
        $a = true ?: false;

        c::m();
        c::$m();
        c::$a;
        c::$a;
        c::aConstant;
    }

    protected function aProtectedMethod()
    {
        $c = null;
        $m = null;
        if (true) {
        }

        $c::m();
        $c::$m();
        $c::$a;
        $c::$a;
    }

    private function aPrivateMethod()
    {
        $great = null;
        $function = function() {};
        echo "This is {$great}";
        echo "This is ${great}";
    }
}
