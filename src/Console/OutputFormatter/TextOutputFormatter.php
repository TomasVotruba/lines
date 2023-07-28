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
Structure
                Non-Static                  %10d (%.2f%%)
                Static                      %10d (%.2f%%)
            Visibility
                Public                      %10d (%.2f%%)
                Protected                   %10d (%.2f%%)
                Private                     %10d (%.2f%%)

        Constants                       %10d
                Global                      %10d (%.2f%%)
                Class                       %10d (%.2f%%)
                Public                      %10d (%.2f%%)
                Non-Public                  %10d (%.2f%%)
END;

        $this->tablePrinter->printItemValueTable([
            ['Namespaces', $measurements->getNamespaces()],
            ['Functions', $measurements->getFunctionCount()],
            ['Interfaces', $measurements->getInterfaces()],
            ['Traits', $measurements->getTraits()],
            // @todo enums
            ['Classes', $measurements->getClasses()],
            ['Methods', $measurements->getMethods()],
        ], 'Structure', 'Count');

        $methods = $measurements->getMethods();

        $result = sprintf(
            $format,

            // methods
            $measurements->getNonStaticMethods(),
            $measurements->getNonStaticMethodsRelative(),
            $measurements->getStaticMethods(),
            $measurements->getStaticMethodsRelative(),
            $publicMethods = $measurements->getPublicMethods(),
            $methods > 0 ? ($publicMethods / $methods) * 100 : 0,
            $protectedMethods = $measurements->getProtectedMethods(),
            $methods > 0 ? ($protectedMethods / $methods) * 100 : 0,
            $privateMethods = $measurements->getPrivateMethods(),
            $methods > 0 ? ($privateMethods / $methods) * 100 : 0,

            // functions
            //            $functions = $measurements->getFunctionCount(),
            //            $namedFunctions = $measurements->getNamedFunctionCount(),
            //            $functions > 0 ? ($namedFunctions / $functions) * 100 : 0,
            //            $anonymousFunctions = $measurements->getAnonymousFunctionCount(),
            //            $functions > 0 ? ($anonymousFunctions / $functions) * 100 : 0,

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
