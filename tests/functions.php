<?php

declare(strict_types=1);

function dd(...$data)
{
    \Tracy\Dumper::dump($data, [
        'depth' => 2,
    ]);
    die;
}
