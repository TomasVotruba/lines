<?php

declare(strict_types=1);

namespace App;

final class PropertyUnionType
{
    private string|int $value;

    public function __construct(string|int $value)
    {
        $this->value = $value;
    }
}
