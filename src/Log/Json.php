<?php declare(strict_types=1);

namespace TomasVotruba\Lines\Log;

final class Json
{
    /**
     * @param array<string, mixed> $count
     */
    public function printResult(string $filename, array $count): void
    {
        $directories = [];

        if ($count['directories'] > 0) {
            $directories = [
                'directories' => $count['directories'],
                'files'       => $count['files'],
            ];
        }

        unset($count['directories'], $count['files']);

        $report = array_merge($directories, $count);

        file_put_contents(
            $filename,
            json_encode($report, JSON_PRETTY_PRINT)
        );
    }
}
