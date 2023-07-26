<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

final class JsonOutputFormatter
{
    /**
     * @param array<string, mixed> $analysisResult
     */
    public function printResult(array $analysisResult, OutputInterface $output): void
    {
        $directories = [];

        if ($analysisResult['directories'] > 0) {
            $directories = [
                'directories' => $analysisResult['directories'],
                'files' => $analysisResult['files'],
            ];
        }

        unset($analysisResult['directories'], $analysisResult['files']);

        $completeReport = array_merge($directories, $analysisResult);

        $jsonString = json_encode($completeReport, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        $output->writeln($jsonString);
    }
}
