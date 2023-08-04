<?php

declare(strict_types=1);

use Tracy\Dumper;

function dd(mixed $data): void
{
    Dumper::dump($data, [
        'depth' => 2,
    ]);
    die;
}
