<?php

class SomeClass
{
    public function someFunction($in)
    {
        $anonymousFunction = function () {
            return 100;
        };
    }

    public function someOtherFunction()
    {
        //trigger Undefined index: ccn
        return false || true;
    }
}
