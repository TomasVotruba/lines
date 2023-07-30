<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Helpers;

final class Calculator
{
    public static function relativeChange(int $firstValue, int $secondValue): float
    {
        $relativeChange = ($secondValue - $firstValue) / $firstValue;

        return $relativeChange * 100;
    }
}
