<?php

declare(strict_types=1);

function pretty_number(int|float $number): string
{
    return number_format($number, 0, '.', ' ');
}

function percent(int|float $number): string
{
    return number_format($number, 1, '.', ' ') . ' %';
}
