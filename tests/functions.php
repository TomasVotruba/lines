<?php

declare(strict_types=1);

use Tracy\Dumper;

function dd(mixed $data): never
{
    Dumper::dump($data, [
        'depth' => 2,
    ]);
    die;
}
