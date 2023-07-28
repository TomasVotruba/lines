<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\Console\TablePrinter;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Helpers\NumberFormat;
use TomasVotruba\Lines\Measurements;

final class TextOutputFormatter implements OutputFormatterInterface
{
    public function __construct(
        private readonly TablePrinter $tablePrinter,
    ) {
    }

    public function printResult(Measurements $measurements, OutputInterface $output): void
    {
        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);

        $this->tablePrinter->printItemValueTable([
            ['Max ', $measurements->getMaxClassLength()],
            ['Average ', $measurements->getAverageClassLength()],
        ], 'Class length', 'Lines');

        $this->tablePrinter->printItemValueTable([
            ['Max', $measurements->getMaxMethodLength()],
            ['Average', $measurements->getAverageMethodLength()],
        ], 'Method length', 'Lines');

        $this->tablePrinter->printItemValueTable([
            ['Classes', $measurements->getClassLines(), $measurements->getClassLinesRelative() . ' %'],
            ['Functions', $measurements->getFunctionLines(), $measurements->getFunctionLinesRelative() . ' %'],
            [
                'Not in classes/functions',
                $measurements->getNotInClassesOrFunctions(),
                $measurements->getNotInClassesOrFunctionsRelative() . ' %',
            ],
        ], 'Classes vs functions vs rest', 'Lines', true);

        $format = <<<'END'
        Constants                       %10d
                Global                      %10d (%.2f%%)
                Class                       %10d (%.2f%%)
                Public                      %10d (%.2f%%)
                Non-Public                  %10d (%.2f%%)
END;

        $this->tablePrinter->printItemValueTable([
            ['Namespaces', $measurements->getNamespaces()],
            ['Classes', $measurements->getClasses()],
            ['Interfaces', $measurements->getInterfaces()],
            ['Traits', $measurements->getTraits()],
            // @todo enums
            ['Methods', $measurements->getMethods()],
            ['Functions', $measurements->getFunctionCount()],
        ], 'Structure', 'Count');

        $this->tablePrinter->printItemValueTable([
            ['Non-static', $measurements->getNonStaticMethods(),
                $measurements->getNonStaticMethodsRelative() . ' %', ],
            ['Static', $measurements->getStaticMethods(), $measurements->getStaticMethodsRelative() . ' %'],

            [new TableSeparator(), new TableSeparator(), new TableSeparator()],

            ['Public', $measurements->getPublicMethods(), $measurements->getPublicMethodsRelative() . ' %'],
            ['Protected', $measurements->getProtectedMethods(), $measurements->getProtectedMethodsRelative() . ' %'],
            ['Private', $measurements->getPrivateMethods(), $measurements->getPrivateMethodsRelative() . ' %'],

        ], 'Methods', 'Count', true);

        $result = sprintf(
            $format,

            // methods

            // constants
            $constants = $measurements->getConstantCount(),
            $globalConstants = $measurements->getGlobalConstantCount(),
            $constants > 0 ? ($globalConstants / $constants) * 100 : 0,
            $classConstants = $measurements->getClassConstants(),
            $constants > 0 ? ($classConstants / $constants) * 100 : 0,
            $publicClassConstants = $measurements->getPublicClassConstants(),
            $classConstants > 0 ? ($publicClassConstants / $classConstants) * 100 : 0,
            $nonPublicClassConstants = $measurements->getNonPublicClassConstants(),
            $classConstants > 0 ? ($nonPublicClassConstants / $classConstants) * 100 : 0
        );

        $output->writeln($result);
    }

    private function printFilesAndDirectories(Measurements $measurements): void
    {
        $tableRows = [['Directories', $measurements->getDirectories()], ['Files', $measurements->getFiles()]];
        $this->tablePrinter->printItemValueTable($tableRows, 'Metric', 'Count');
    }

    private function printLinesOfCode(Measurements $measurements): void
    {
        $tableRows = [
            [
                'Code',
                NumberFormat::pretty($measurements->getNonCommentLines()),
                $measurements->getNonCommentLinesRelative() . ' %',
            ],

            [
                'Comments',
                NumberFormat::pretty($measurements->getCommentLines()),
                $measurements->getCommentLinesRelative() . ' %',
            ],

            [new TableSeparator(), new TableSeparator(), new TableSeparator()],

            [
                '<options=bold>Total</>',
                '<options=bold>' . NumberFormat::pretty($measurements->getLines()) . '</>',
                '<options=bold>100.0 %</>',
            ],
        ];

        $this->tablePrinter->printItemValueTable($tableRows, 'Lines of code', 'Count', true);
    }
}
