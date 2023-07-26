<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Log;

final class Json
{
    /**
     * @param array<string, mixed> $count
     */
    public function printResult(array $count): void
    {
        $directories = [];

        if ($count['directories'] > 0) {
            $directories = [
                'directories' => $count['directories'],
                'files' => $count['files'],
            ];
        }

        unset($count['directories'], $count['files']);

        $report = array_merge($directories, $count);
        $json = json_encode($report, JSON_PRETTY_PRINT);

        echo $json . PHP_EOL;
    }
}
