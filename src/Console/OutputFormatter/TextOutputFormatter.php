<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\Console\TablePrinter;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Measurements;

final class TextOutputFormatter implements OutputFormatterInterface
{
    public function __construct(
        private readonly TablePrinter $tablePrinter,
    ) {
    }

    public function printMeasurement(Measurements $measurements, OutputInterface $output, bool $isShort): void
    {
        // newline
        $output->writeln('');

        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);

        if ($isShort) {
            return;
        }

        $this->tablePrinter->printItemValueTable([
            ['Class max', $measurements->getMaxClassLength()],
            ['Class average', $measurements->getAverageClassLength()],
            ['Method max', $measurements->getMaxMethodLength()],
            ['Method average', $measurements->getAverageMethodLength()],
        ], 'Length Stats', 'Lines');

        $this->tablePrinter->printItemValueTable([
            ['Classes', $measurements->getClassLines(), $measurements->getClassLinesRelative()],
            ['Functions', $measurements->getFunctionLines(), $measurements->getFunctionLinesRelative()],
            [
                'Not in classes/functions',
                $measurements->getNotInClassesOrFunctions(),
                $measurements->getNotInClassesOrFunctionsRelative(),
            ],
        ], 'Classes vs functions vs rest', 'Lines', true);

        $this->printStructure($measurements);
        $this->printMethods($measurements);
        $this->printConstants($measurements);
    }

    private function printFilesAndDirectories(Measurements $measurements): void
    {
        $tableRows = [['Directories', $measurements->getDirectoryCount()], ['Files', $measurements->getFileCount()]];

        $this->tablePrinter->printItemValueTable($tableRows, 'Metric', 'Count');
    }

    private function printLinesOfCode(Measurements $measurements): void
    {
        $tableRows = [
            ['Code', $measurements->getNonCommentLines(), $measurements->getNonCommentLinesRelative()],
            ['Comments', $measurements->getCommentLines(), $measurements->getCommentLinesRelative()],
            ['Total', $measurements->getLines(), 100.0],
        ];

        $this->tablePrinter->printItemValueTable($tableRows, 'Lines of code', 'Count', true);
    }

    private function printMethods(Measurements $measurements): void
    {
        if ($measurements->getMethodCount() === 0) {
            return;
        }

        $this->tablePrinter->printItemValueTable([
            ['Non-static', $measurements->getNonStaticMethods(), $measurements->getNonStaticMethodsRelative()],
            ['Static', $measurements->getStaticMethods(), $measurements->getStaticMethodsRelative()],

            new TableSeparator(),

            ['Public', $measurements->getPublicMethods(), $measurements->getPublicMethodsRelative()],
            ['Protected', $measurements->getProtectedMethods(), $measurements->getProtectedMethodsRelative()],
            ['Private', $measurements->getPrivateMethods(), $measurements->getPrivateMethodsRelative()],

        ], 'Methods', 'Count', true);
    }

    private function printStructure(Measurements $measurements): void
    {
        $this->tablePrinter->printItemValueTable([
            ['Namespaces', $measurements->getNamespaces()],
            ['Classes', $measurements->getClassCount()],
            ['Interfaces', $measurements->getInterfaceCount()],
            ['Traits', $measurements->getTraitCount()],
            ['Enums', $measurements->getEnumCount()],
            ['Constants', $measurements->getConstantCount()],
            ['Methods', $measurements->getMethodCount()],
            ['Functions', $measurements->getFunctionCount()],
        ], 'Structure', 'Count');
    }

    private function printConstants(Measurements $measurements): void
    {
        if ($measurements->getConstantCount() === 0) {
            return;
        }

        $constantsRows = [
            ['Global', $measurements->getGlobalConstantCount(), $measurements->getGlobalConstantCountRelative()],
            ['Class', $measurements->getClassConstants(), $measurements->getClassConstantCountRelative()],
        ];

        if ($measurements->getClassConstants() !== 0) {
            $constantsRows[] = new TableSeparator();

            $constantsRows[] = [
                'Non-public',
                $measurements->getNonPublicClassConstants(),
                $measurements->getNonPublicClassConstantsRelative(),
            ];
        }

        $this->tablePrinter->printItemValueTable($constantsRows, 'Constants', 'Count', true);
    }
}
